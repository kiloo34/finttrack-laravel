import { useState, useEffect } from 'react';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle,
    DialogFooter
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Combobox } from '@/components/ui/combobox';
import axios from '@/lib/axios';
import { usePage } from '@inertiajs/react';
import { Account } from '@/types/finance';

interface AccountModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    account?: Account | null; // If provided, we are editing
}

export default function AccountModal({ isOpen, onClose, onSuccess, account }: AccountModalProps) {
    const { constants } = usePage<any>().props;
    const ACCOUNT_TYPES = constants?.ACCOUNT_TYPES || [];
    const CURRENCIES = constants?.CURRENCIES || [];
    const PROVIDERS = constants?.PROVIDERS || [];
    const [isLoading, setIsLoading] = useState(false);
    
    // Form state
    const [name, setName] = useState('');
    const [type, setType] = useState('bank');
    const [currency, setCurrency] = useState('IDR');
    const [providerId, setProviderId] = useState<string>('');
    const [accountNumber, setAccountNumber] = useState('');
    const [initialBalance, setInitialBalance] = useState('');
    const [syncFrequency, setSyncFrequency] = useState('manual');
    const [errors, setErrors] = useState<Record<string, string[]>>({});

    useEffect(() => {
        if (isOpen) {
            if (account) {
                setName(account.name);
                setType(account.type);
                setCurrency(account.currency);
                setProviderId(account.provider_id ? account.provider_id.toString() : '');
                setAccountNumber(account.account_number || '');
                setInitialBalance(account.initial_balance.toString());
                setSyncFrequency(account.sync_frequency || 'manual');
            } else {
                resetForm();
            }
        }
    }, [isOpen, account]);

    const resetForm = () => {
        setName('');
        setType('bank');
        setCurrency('IDR');
        setProviderId('');
        setAccountNumber('');
        setInitialBalance('');
        setSyncFrequency('manual');
        setErrors({});
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setErrors({});

        try {
            const payload = {
                name,
                type,
                currency,
                provider_id: providerId ? parseInt(providerId, 10) : null,
                account_number: accountNumber || null,
                initial_balance: parseFloat(initialBalance) || 0,
                is_active: true,
                sync_frequency: syncFrequency
            };
            if (account) {
                await axios.put(`/accounts/${account.id}`, payload);
            } else {
                await axios.post('/accounts', payload);
            }
            
            onSuccess();
            onClose();
        } catch (error: any) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                console.error("Submission failed", error);
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{account ? 'Edit Account' : 'Add Account'}</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4 py-4">
                    
                    <div className="space-y-2">
                        <Label htmlFor="name">Account Name</Label>
                        <Input 
                            id="name" 
                            value={name} 
                            onChange={(e) => setName(e.target.value)} 
                            placeholder="e.g. TabunganKu, Payroll, Main Wallet"
                        />
                        {errors.name && <p className="text-sm text-red-500">{errors.name[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="provider_id">Provider / Bank (Optional)</Label>
                        <Combobox
                            options={PROVIDERS.map((p: any) => ({ label: p.name, value: p.id.toString() }))}
                            value={providerId}
                            onChange={setProviderId}
                            placeholder="Select Provider (e.g. BCA, Mandiri)"
                        />
                        {errors.provider_id && <p className="text-sm text-red-500">{errors.provider_id[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="type">Account Type</Label>
                        <Combobox
                            options={ACCOUNT_TYPES}
                            value={type}
                            onChange={setType}
                            placeholder="Select type"
                        />
                        {errors.type && <p className="text-sm text-red-500">{errors.type[0]}</p>}
                    </div>

                    {type === 'investment' && (
                        <div className="space-y-2">
                            <Label htmlFor="syncFrequency">Sync Frequency</Label>
                            <Combobox
                                options={[
                                    { label: 'Manual', value: 'manual' },
                                    { label: 'Daily', value: 'daily' },
                                    { label: 'Weekly', value: 'weekly' },
                                    { label: 'Monthly', value: 'monthly' }
                                ]}
                                value={syncFrequency}
                                onChange={setSyncFrequency}
                                placeholder="Select sync frequency"
                            />
                            {errors.sync_frequency && <p className="text-sm text-red-500">{errors.sync_frequency[0]}</p>}
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="currency">Currency</Label>
                        <Combobox
                            options={CURRENCIES}
                            value={currency}
                            onChange={setCurrency}
                            placeholder="Select currency"
                        />
                        {errors.currency && <p className="text-sm text-red-500">{errors.currency[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="accountNumber">Account/Phone Number</Label>
                        <Input 
                            id="accountNumber" 
                            value={accountNumber} 
                            onChange={(e) => setAccountNumber(e.target.value)} 
                            placeholder="e.g. 1234567890"
                        />
                        {errors.account_number && <p className="text-sm text-red-500">{errors.account_number[0]}</p>}
                    </div>

                    {!account && (
                        <div className="space-y-2">
                            <Label htmlFor="initialBalance">Initial Balance</Label>
                            <Input 
                                id="initialBalance" 
                                type="number" 
                                value={initialBalance} 
                                onChange={(e) => setInitialBalance(e.target.value)} 
                                placeholder="0"
                            />
                            {errors.initial_balance && <p className="text-sm text-red-500">{errors.initial_balance[0]}</p>}
                        </div>
                    )}

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isLoading}>
                            {isLoading ? 'Saving...' : (account ? 'Update Account' : 'Save Account')}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
