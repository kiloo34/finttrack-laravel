import { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Account, Category } from '@/types/finance';
import axios from '@/lib/axios';
import { AlertCircle, RefreshCw } from 'lucide-react';

interface SyncBalanceModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    account: Account | null;
}

export default function SyncBalanceModal({ isOpen, onClose, onSuccess, account }: SyncBalanceModalProps) {
    const [actualBalance, setActualBalance] = useState('');
    const [categories, setCategories] = useState<Category[]>([]);
    const [categoryId, setCategoryId] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        if (isOpen && account) {
            setActualBalance(String(account.current_balance));
            setCategoryId('');
            setError('');
            fetchCategories();
        }
    }, [isOpen, account]);

    const fetchCategories = async () => {
        try {
            const res = await axios.get<{ data: Category[] }>('/categories');
            setCategories(res.data.data);
        } catch (e) {
            console.error('Failed to fetch categories', e);
        }
    };

    const handleSync = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!account) return;

        const numActualBalance = parseFloat(actualBalance);
        if (isNaN(numActualBalance)) {
            setError('Masukkan saldo yang valid.');
            return;
        }

        const discrepancy = numActualBalance - Number(account.current_balance);
        if (discrepancy !== 0 && !categoryId) {
            setError('Pilih kategori untuk transaksi penyesuaian.');
            return;
        }

        setIsSubmitting(true);
        setError('');

        try {
            await axios.post(`/accounts/${account.id}/sync-balance`, {
                actual_balance: actualBalance,
                category_id: categoryId ? parseInt(categoryId) : undefined
            });
            onSuccess();
            onClose();
        } catch (err: any) {
            if (err.response?.data?.errors?.sync) {
                setError(err.response.data.errors.sync[0]);
            } else if (err.response?.data?.message) {
                setError(err.response.data.message);
            } else {
                setError('Terjadi kesalahan saat sinkronisasi saldo.');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    if (!account) return null;

    const currentBalance = Number(account.current_balance);
    const numActual = parseFloat(actualBalance) || 0;
    const discrepancy = numActual - currentBalance;
    const isExpense = discrepancy < 0;
    const discrepancyFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: account.currency }).format(Math.abs(discrepancy));
    
    // Filter categories based on discrepancy type
    const filteredCategories = categories.filter(c => discrepancy === 0 || (isExpense ? c.type === 'expense' : c.type === 'income'));

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <RefreshCw className="h-5 w-5" />
                        Sync Balance
                    </DialogTitle>
                    <DialogDescription>
                        Sesuaikan saldo akun <b>{account.name}</b>. Transaksi penyesuaian akan otomatis dibuat jika ada selisih.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSync} className="space-y-4 py-4">
                    {error && (
                        <div className="p-3 text-sm text-destructive-foreground bg-destructive/10 rounded-md flex items-start gap-2 border border-destructive/20">
                            <AlertCircle className="h-4 w-4 mt-0.5 shrink-0" />
                            <span>{error}</span>
                        </div>
                    )}

                    <div className="space-y-1">
                        <Label>Saldo Sistem Saat Ini</Label>
                        <div className="font-semibold text-lg text-muted-foreground bg-muted p-2 rounded-md">
                            {new Intl.NumberFormat('id-ID', { style: 'currency', currency: account.currency }).format(currentBalance)}
                        </div>
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="actual_balance">Saldo Aktual (Sebenarnya)</Label>
                        <Input
                            id="actual_balance"
                            type="number"
                            step="0.01"
                            value={actualBalance}
                            onChange={(e) => setActualBalance(e.target.value)}
                            required
                        />
                    </div>

                    {discrepancy !== 0 && (
                        <div className={`p-3 rounded-md text-sm border ${isExpense ? 'bg-red-50 border-red-200 text-red-900 dark:bg-red-950/50 dark:border-red-900 dark:text-red-200' : 'bg-green-50 border-green-200 text-green-900 dark:bg-green-950/50 dark:border-green-900 dark:text-green-200'}`}>
                            <p className="font-medium mb-1">
                                {isExpense ? 'Selisih Kurang (Expense)' : 'Selisih Lebih (Income)'}: {discrepancyFormatted}
                            </p>
                            <div className="space-y-1 mt-2">
                                <Label htmlFor="category_id">Pilih Kategori Penyesuaian</Label>
                                <select 
                                    id="category_id"
                                    className="flex h-9 w-full items-center justify-between whitespace-nowrap rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    value={categoryId}
                                    onChange={(e) => setCategoryId(e.target.value)}
                                    required
                                >
                                    <option value="" disabled>-- Pilih Kategori --</option>
                                    {filteredCategories.map(cat => (
                                        <option key={cat.id} value={cat.id}>{cat.name}</option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    )}

                    <DialogFooter className="pt-2">
                        <Button type="button" variant="outline" onClick={onClose} disabled={isSubmitting}>
                            Batal
                        </Button>
                        <Button type="submit" disabled={isSubmitting || (discrepancy !== 0 && !categoryId)}>
                            {isSubmitting ? 'Menyinkronkan...' : 'Sync Sekarang'}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
