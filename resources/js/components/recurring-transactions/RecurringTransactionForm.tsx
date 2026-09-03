import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Combobox } from '@/components/ui/combobox';
import { Switch } from '@/components/ui/switch';
import axios from '@/lib/axios';
import { toast } from 'sonner';

interface RecurringTransactionFormProps {
    accounts: any[];
    categories: any[];
    schedule?: any;
    onSuccess?: () => void;
    onCancel?: () => void;
}

export default function RecurringTransactionForm({ accounts, categories, schedule, onSuccess, onCancel }: RecurringTransactionFormProps) {
    const [loading, setLoading] = useState(false);
    const [validationErrors, setValidationErrors] = useState<Record<string, string[]>>({});
    const { data, setData, reset } = useForm({
        account_id: schedule?.accountId?.toString() || '',
        destination_account_id: schedule?.destinationAccountId?.toString() || '',
        category_id: schedule?.categoryId?.toString() || '',
        type: schedule?.type || 'expense',
        amount: schedule?.amount ? Number(schedule.amount).toString() : '',
        currency: schedule?.currency || 'IDR',
        description: schedule?.description || '',
        frequency: schedule?.frequency || 'monthly',
        execution_type: schedule?.executionType || 'draft',
        next_run_date: schedule?.nextRunDate || new Date().toISOString().split('T')[0],
        is_active: schedule?.isActive ?? true,
    });

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        // Frontend Validation
        const errors: Record<string, string[]> = {};
        
        if (!data.account_id) {
            errors.account_id = ['Please select a source account.'];
        }
        
        if (data.type === 'transfer') {
            if (!data.destination_account_id) {
                errors.destination_account_id = ['Please select a destination account.'];
            } else if (data.account_id && data.destination_account_id && data.account_id === data.destination_account_id) {
                errors.destination_account_id = ['Destination account cannot be the same as the source account.'];
            }
        } else {
            if (!data.category_id) {
                errors.category_id = ['Please select a category.'];
            }
        }
        
        if (!data.amount || isNaN(Number(data.amount)) || Number(data.amount) <= 0) {
            errors.amount = ['Amount must be greater than 0.'];
        }
        
        if (!data.next_run_date) {
            errors.next_run_date = ['Please select a next run date.'];
        }
        
        if (Object.keys(errors).length > 0) {
            setValidationErrors(errors);
            toast.error('Validation failed', { description: 'Please check the form for errors.' });
            return;
        }

        setLoading(true);
        
        try {
            const payload = {
                ...data,
                account_id: parseInt(data.account_id),
                destination_account_id: data.destination_account_id ? parseInt(data.destination_account_id) : null,
                category_id: data.category_id ? parseInt(data.category_id) : null,
            };

            if (schedule) {
                await axios.put(`/recurring-transactions/${schedule.id}`, payload);
                toast.success('Schedule updated', { description: 'The recurring schedule has been updated successfully.' });
            } else {
                await axios.post('/recurring-transactions', payload);
                toast.success('Schedule created', { description: 'A new recurring schedule has been created.' });
            }
            
            reset();
            setValidationErrors({});
            if (onSuccess) onSuccess();
        } catch (error: any) {
            console.error(error);
            if (error.response?.status === 422) {
                setValidationErrors(error.response.data.errors || {});
                toast.error('Validation failed', { description: 'Please check the form for errors.' });
            } else {
                toast.error('Failed to save', { description: 'An error occurred while saving the schedule.' });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                    <Label htmlFor="type">Transaction Type</Label>
                    <Select value={data.type} onValueChange={(val) => setData('type', val)}>
                        <SelectTrigger>
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="expense">Expense</SelectItem>
                            <SelectItem value="income">Income</SelectItem>
                            <SelectItem value="transfer">Transfer</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-2">
                    <Label htmlFor="amount">Amount</Label>
                    <Input
                        id="amount"
                        type="number"
                        step="0.01"
                        value={data.amount}
                        onChange={(e) => setData('amount', e.target.value)}
                        placeholder="0.00"
                        required
                    />
                    {validationErrors.amount && <p className="text-xs text-red-500 mt-1">{validationErrors.amount[0]}</p>}
                </div>

                <div className="space-y-2 flex flex-col">
                    <Label htmlFor="account_id">Source Account</Label>
                    <Combobox
                        options={accounts.map(acc => ({
                            label: acc.account_number ? `${acc.name} - ${acc.account_number}` : acc.name,
                            value: acc.id.toString(),
                            group: acc.type.replace('_', ' ').toUpperCase()
                        }))}
                        value={data.account_id}
                        onChange={(val) => setData('account_id', val)}
                        placeholder="Select account..."
                    />
                    {validationErrors.account_id && <p className="text-xs text-red-500 mt-1">{validationErrors.account_id[0]}</p>}
                </div>

                {data.type === 'transfer' ? (
                    <div className="space-y-2 flex flex-col">
                        <Label htmlFor="destination_account_id">Destination Account</Label>
                        <Combobox
                            options={accounts.filter(a => a.id.toString() !== data.account_id).map(acc => ({
                                label: acc.account_number ? `${acc.name} - ${acc.account_number}` : acc.name,
                                value: acc.id.toString(),
                                group: acc.type.replace('_', ' ').toUpperCase()
                            }))}
                            value={data.destination_account_id}
                            onChange={(val) => setData('destination_account_id', val)}
                            placeholder="Select destination..."
                        />
                        {validationErrors.destination_account_id && <p className="text-xs text-red-500 mt-1">{validationErrors.destination_account_id[0]}</p>}
                    </div>
                ) : (
                    <div className="space-y-2">
                        <Label htmlFor="category_id">Category</Label>
                        <Select value={data.category_id} onValueChange={(val) => setData('category_id', val)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select category" />
                            </SelectTrigger>
                            <SelectContent>
                                {categories.filter(c => c.type === data.type).map(cat => (
                                    <SelectItem key={cat.id} value={cat.id.toString()}>{cat.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {validationErrors.category_id && <p className="text-xs text-red-500 mt-1">{validationErrors.category_id[0]}</p>}
                    </div>
                )}

                <div className="space-y-2">
                    <Label htmlFor="description">Description</Label>
                    <Input
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        placeholder="e.g. Monthly Netflix"
                    />
                    {validationErrors.description && <p className="text-xs text-red-500 mt-1">{validationErrors.description[0]}</p>}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="frequency">Frequency</Label>
                    <Select value={data.frequency} onValueChange={(val) => setData('frequency', val)}>
                        <SelectTrigger>
                            <SelectValue placeholder="Select frequency" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="daily">Daily</SelectItem>
                            <SelectItem value="workdays">Workdays (Hari Kerja)</SelectItem>
                            <SelectItem value="weekly">Weekly</SelectItem>
                            <SelectItem value="monthly">Monthly</SelectItem>
                            <SelectItem value="yearly">Yearly</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-2">
                    <Label htmlFor="next_run_date">Next Run Date</Label>
                    <Input
                        id="next_run_date"
                        type="date"
                        value={data.next_run_date}
                        onChange={(e) => setData('next_run_date', e.target.value)}
                        required
                    />
                    {validationErrors.next_run_date && <p className="text-xs text-red-500 mt-1">{validationErrors.next_run_date[0]}</p>}
                </div>

                <div className="space-y-2">
                    <Label htmlFor="execution_type">Execution Mode</Label>
                    <Select value={data.execution_type} onValueChange={(val) => setData('execution_type', val)}>
                        <SelectTrigger>
                            <SelectValue placeholder="Select mode" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="draft">Draft (Needs manual confirmation)</SelectItem>
                            <SelectItem value="auto_complete">Auto-Complete (Executes immediately)</SelectItem>
                        </SelectContent>
                    </Select>
                    <p className="text-xs text-muted-foreground mt-1">
                        {data.execution_type === 'draft' 
                            ? 'Transactions will appear in your dashboard as "Pending" until you confirm them.'
                            : 'Transactions will be immediately deducted from your balance.'}
                    </p>
                </div>
            </div>

            <div className="flex items-center space-x-2">
                <Switch 
                    id="is_active" 
                    checked={data.is_active} 
                    onCheckedChange={(checked) => setData('is_active', checked)} 
                />
                <Label htmlFor="is_active">Active Schedule</Label>
            </div>

            <div className="flex justify-end gap-2 pt-4">
                {onCancel && (
                    <Button type="button" variant="outline" onClick={onCancel}>
                        Cancel
                    </Button>
                )}
                <Button type="submit" disabled={loading}>
                    {loading ? 'Saving...' : 'Save Schedule'}
                </Button>
            </div>
        </form>
    );
}
