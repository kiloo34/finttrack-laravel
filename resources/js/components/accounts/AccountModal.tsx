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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import axios from '@/lib/axios';
import { Account } from '@/types/finance';

interface AccountModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    account?: Account | null; // If provided, we are editing
}

export default function AccountModal({ isOpen, onClose, onSuccess, account }: AccountModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    
    // Form state
    const [name, setName] = useState('');
    const [type, setType] = useState('bank');
    const [currency, setCurrency] = useState('IDR');
    const [initialBalance, setInitialBalance] = useState('');
    const [errors, setErrors] = useState<Record<string, string[]>>({});

    useEffect(() => {
        if (isOpen) {
            if (account) {
                setName(account.name);
                setType(account.type);
                setCurrency(account.currency);
                setInitialBalance(account.initial_balance.toString());
            } else {
                resetForm();
            }
        }
    }, [isOpen, account]);

    const resetForm = () => {
        setName('');
        setType('bank');
        setCurrency('IDR');
        setInitialBalance('');
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
                initial_balance: parseFloat(initialBalance) || 0,
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
                            placeholder="e.g. BCA, OVO, Cash"
                        />
                        {errors.name && <p className="text-sm text-red-500">{errors.name[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="type">Account Type</Label>
                        <Select value={type} onValueChange={setType}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select type" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="bank">Bank</SelectItem>
                                <SelectItem value="e-wallet">E-Wallet</SelectItem>
                                <SelectItem value="cash">Cash</SelectItem>
                                <SelectItem value="credit_card">Credit Card</SelectItem>
                                <SelectItem value="investment">Investment</SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.type && <p className="text-sm text-red-500">{errors.type[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="currency">Currency</Label>
                        <Select value={currency} onValueChange={setCurrency}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select currency" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="IDR">IDR</SelectItem>
                                <SelectItem value="USD">USD</SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.currency && <p className="text-sm text-red-500">{errors.currency[0]}</p>}
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
