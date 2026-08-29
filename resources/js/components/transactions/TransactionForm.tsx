import { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Combobox } from '@/components/ui/combobox';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import axios from '@/lib/axios';
import { usePage } from '@inertiajs/react';
import { Account, Category, Transaction } from '@/types/finance';
import { Check, Loader2 } from 'lucide-react';
import InputError from '@/components/input-error';
import AmountDisplay from '@/components/AmountDisplay';

interface TransactionFormProps {
    initialData?: Transaction;
}

export default function TransactionForm({ initialData }: TransactionFormProps) {
    const { constants } = usePage<any>().props;
    const TRANSACTION_TYPES = constants?.TRANSACTION_TYPES || [];
    const isEditing = !!initialData;
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [isFetching, setIsFetching] = useState(true);

    // Form state
    const [accountId, setAccountId] = useState(initialData?.account_id?.toString() || '');
    const [categoryId, setCategoryId] = useState(initialData?.category_id?.toString() || '');
    const [type, setType] = useState(initialData?.type || 'expense');
    const [amount, setAmount] = useState(initialData?.amount?.toString() || '');
    const [date, setDate] = useState(initialData?.transaction_date ? new Date(initialData.transaction_date).toISOString().split('T')[0] : new Date().toISOString().split('T')[0]);
    const [description, setDescription] = useState(initialData?.description || '');
    
    // For transfers
    const [toAccountId, setToAccountId] = useState('');
    // Note: If initialData is a transfer, we'd need to extract from_account_id and to_account_id.
    // The backend stores transfer transaction records differently or as related records. 
    // For simplicity, we just handle standard transaction edits for now.
    
    const [errors, setErrors] = useState<Record<string, string[]>>({});

    useEffect(() => {
        fetchDependencies();
    }, []);

    const fetchDependencies = async () => {
        try {
            const [accountsRes, categoriesRes] = await Promise.all([
                axios.get<{ data: Account[] }>('/accounts'),
                axios.get<{ data: Category[] }>('/categories')
            ]);
            setAccounts(accountsRes.data.data);
            setCategories(categoriesRes.data.data);
        } catch (error) {
            console.error('Error fetching dependencies', error);
        } finally {
            setIsFetching(false);
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setErrors({});

        try {
            const payload = type === 'transfer' ? {
                from_account_id: accountId,
                to_account_id: toAccountId,
                amount: parseFloat(amount),
                currency: 'IDR',
                transaction_date: date,
                description
            } : {
                account_id: accountId,
                category_id: categoryId || null,
                type,
                amount: parseFloat(amount),
                currency: 'IDR',
                transaction_date: date,
                description
            };

            if (isEditing) {
                // If the backend has a different endpoint for updating transfers vs transactions, handle it here.
                // Assuming standard resourceful updates for transactions:
                await axios.put(`/transactions/${initialData.id}`, payload);
            } else {
                if (type === 'transfer') {
                    await axios.post('/transfers', payload);
                } else {
                    await axios.post('/transactions', payload);
                }
            }
            
            router.visit('/transactions');
        } catch (error: any) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                console.error("Submission failed", error);
            }
            setIsLoading(false);
        }
    };

    // Build hierarchical category options using Combobox groups
    const filteredCategories = categories.filter(c => c.type === type);
    const categoryOptions: { label: string, value: string, group?: string }[] = [];
    
    const rootCategories = filteredCategories.filter(c => c.parent_id === null);
    
    // First, add all root categories that have NO children as ungrouped options
    rootCategories.forEach(root => {
        const children = filteredCategories.filter(c => c.parent_id === root.id);
        if (children.length === 0) {
            categoryOptions.push({ label: root.name, value: root.id.toString() });
        } else {
            // For root categories with children, add them to their own group 
            // so they can be selected too, then add their children.
            categoryOptions.push({ 
                label: `${root.name} (Top Level)`, 
                value: root.id.toString(),
                group: root.name
            });
            
            children.forEach(child => {
                categoryOptions.push({ 
                    label: child.name, 
                    value: child.id.toString(),
                    group: root.name
                });
            });
        }
    });

    if (isFetching) {
        return (
            <div className="flex h-64 items-center justify-center">
                <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
            </div>
        );
    }

    return (
        <form onSubmit={handleSubmit} className="relative flex flex-col flex-1 h-full">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-3">
                
                {/* Left Column */}
                <div className="md:col-span-2 space-y-6">
                    {/* Transaction Details */}
                    <Card className="shadow-sm">
                        <CardHeader>
                            <CardTitle>Transaction details</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="description">Description</Label>
                                <Input 
                                    id="description" 
                                    value={description} 
                                    onChange={(e) => setDescription(e.target.value)} 
                                    placeholder="e.g. Grocery shopping"
                                />
                                {errors.description && <InputError message={errors.description[0]} />}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="date">Date</Label>
                                <Input 
                                    id="date" 
                                    type="date" 
                                    value={date} 
                                    onChange={(e) => setDate(e.target.value)} 
                                    className="block w-full"
                                />
                                {errors.transaction_date && <InputError message={errors.transaction_date[0]} />}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Financials */}
                    <Card className="shadow-sm">
                        <CardHeader>
                            <CardTitle>Financials</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="amount">Amount (IDR)</Label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                                        Rp
                                    </div>
                                    <Input 
                                        id="amount" 
                                        type="number" 
                                        value={amount} 
                                        onChange={(e) => setAmount(e.target.value)} 
                                        placeholder="0.00"
                                        min="0"
                                        step="0.01"
                                        className="pl-9"
                                    />
                                </div>
                                {errors.amount && <InputError message={errors.amount[0]} />}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Routing / Accounts */}
                    <Card className="shadow-sm">
                        <CardHeader>
                            <CardTitle>Accounts</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="account">{type === 'transfer' ? 'Source Account' : 'Account'}</Label>
                                <Select value={accountId} onValueChange={setAccountId}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select an account" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {accounts.map(account => (
                                            <SelectItem key={account.id} value={account.id.toString()}>
                                                {account.name} {account.account_number ? `(${account.account_number})` : ''} - <AmountDisplay amount={Number(account.current_balance)} />
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.account_id && <InputError message={errors.account_id[0]} />}
                                {errors.from_account_id && <InputError message={errors.from_account_id[0]} />}
                            </div>

                            {type === 'transfer' && (
                                <div className="space-y-2">
                                    <Label htmlFor="to_account">Destination Account</Label>
                                    <Select value={toAccountId} onValueChange={setToAccountId}>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select destination account" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {accounts
                                                .filter(a => a.id.toString() !== accountId)
                                                .map(account => (
                                                <SelectItem key={account.id} value={account.id.toString()}>
                                                    {account.name} {account.account_number ? `(${account.account_number})` : ''} - <AmountDisplay amount={Number(account.current_balance)} />
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.to_account_id && <InputError message={errors.to_account_id[0]} />}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Right Column */}
                <div className="md:col-span-1 space-y-6">
                    {/* Status / Type */}
                    <Card className="shadow-sm">
                        <CardHeader>
                            <CardTitle>Transaction Type</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="type">Type</Label>
                                <Combobox
                                    options={TRANSACTION_TYPES}
                                    value={type}
                                    onChange={setType}
                                    placeholder="Select type"
                                    disabled={isEditing}
                                />
                                {errors.type && <InputError message={errors.type[0]} />}
                                {isEditing && <p className="text-xs text-muted-foreground mt-1">Transaction type cannot be changed after creation.</p>}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Organization / Category */}
                    {type !== 'transfer' && (
                        <Card className="shadow-sm">
                            <CardHeader>
                                <CardTitle>Classification</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="category">Category (Optional)</Label>
                                    <Combobox
                                        options={categoryOptions}
                                        value={categoryId}
                                        onChange={setCategoryId}
                                        placeholder="Select a category"
                                    />
                                    {errors.category_id && <InputError message={errors.category_id[0]} />}
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>

            {/* Sticky Footer */}
            <div className="sticky bottom-0 z-50 flex items-center justify-end gap-3 pt-8 mt-auto -mx-2 sm:-mx-6 px-2 sm:px-6">
                <Button 
                    type="button" 
                    variant="outline" 
                    onClick={() => router.visit('/transactions')}
                >
                    Cancel
                </Button>
                <Button type="submit" disabled={isLoading} className="bg-blue-600 hover:bg-blue-700 text-white">
                    {isLoading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
                    {isEditing ? 'Save changes' : 'Save transaction'}
                </Button>
            </div>
        </form>
    );
}
