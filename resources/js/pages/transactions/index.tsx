import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from '@/lib/axios';
import { Transaction } from '@/types/finance';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Plus, ArrowRightLeft, TrendingUp, TrendingDown, Pencil, CheckCircle2 } from 'lucide-react';
import { format } from 'date-fns';
import AmountDisplay from '@/components/AmountDisplay';
import { toast } from 'sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Search } from 'lucide-react';

export default function TransactionsIndex() {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    const [search, setSearch] = useState('');
    const [type, setType] = useState('all');
    const [status, setStatus] = useState('all');
    
    const today = format(new Date(), 'yyyy-MM-dd');
    const [startDate, setStartDate] = useState(today);
    const [endDate, setEndDate] = useState(today);

    const [page, setPage] = useState(1);
    const [perPage, setPerPage] = useState('10');
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const fetchTransactions = async () => {
        try {
            setIsLoading(true);
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (type !== 'all') params.append('type', type);
            if (status !== 'all') params.append('status', status);
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            params.append('page', page.toString());
            params.append('per_page', perPage);

            const res = await axios.get<{ data: Transaction[], meta: any }>(`/transactions?${params.toString()}`);
            setTransactions(res.data.data.filter((t: Transaction) => !t.reference?.startsWith('REC-IN-')));
            if (res.data.meta) {
                setMeta(res.data.meta);
            }
        } catch (error) {
            console.error("Failed to fetch transactions", error);
        } finally {
            setIsLoading(false);
        }
    };

    const [confirmTransaction, setConfirmTransaction] = useState<Transaction | null>(null);
    const [confirmAmount, setConfirmAmount] = useState<string>('');

    const handleConfirmSubmit = async () => {
        if (!confirmTransaction) return;
        try {
            await axios.put(`/transactions/${confirmTransaction.id}`, {
                ...confirmTransaction,
                amount: confirmAmount ? Number(confirmAmount) : confirmTransaction.amount,
                status: 'completed'
            });
            toast.success('Transaction confirmed!');
            setConfirmTransaction(null);
            fetchTransactions();
        } catch (error) {
            console.error("Failed to confirm transaction", error);
            toast.error('Failed to confirm transaction.');
        }
    };

    useEffect(() => {
        const delayDebounceFn = setTimeout(() => {
            fetchTransactions();
        }, 300);

        return () => clearTimeout(delayDebounceFn);
    }, [search, type, status, startDate, endDate, page, perPage]);

    // Reset page to 1 when filters change
    useEffect(() => {
        setPage(1);
    }, [search, type, status, startDate, endDate, perPage]);

    return (
        <>
            <Head title="Transactions" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold tracking-tight">Transactions</h1>
                    <Button onClick={() => router.visit('/transactions/create')}>
                        <Plus className="mr-2 h-4 w-4" /> Add Transaction
                    </Button>
                </div>

                {/* Filters */}
                <div className="flex flex-col sm:flex-row gap-4 mb-2">
                    <div className="relative flex-1">
                        <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                        <Input
                            type="text"
                            placeholder="Search description or reference..."
                            className="pl-9"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                    <div className="flex gap-4">
                        <Input
                            type="date"
                            value={startDate}
                            onChange={(e) => setStartDate(e.target.value)}
                            className="w-[140px] text-muted-foreground"
                            title="Start Date"
                        />
                        <Input
                            type="date"
                            value={endDate}
                            onChange={(e) => setEndDate(e.target.value)}
                            className="w-[140px] text-muted-foreground"
                            title="End Date"
                        />
                        <Select value={type} onValueChange={setType}>
                            <SelectTrigger className="w-[140px]">
                                <SelectValue placeholder="All Types" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Types</SelectItem>
                                <SelectItem value="income">Income</SelectItem>
                                <SelectItem value="expense">Expense</SelectItem>
                                <SelectItem value="transfer">Transfer</SelectItem>
                            </SelectContent>
                        </Select>
                        <Select value={status} onValueChange={setStatus}>
                            <SelectTrigger className="w-[140px]">
                                <SelectValue placeholder="All Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">All Status</SelectItem>
                                <SelectItem value="completed">Completed</SelectItem>
                                <SelectItem value="pending">Pending</SelectItem>
                                <SelectItem value="cancelled">Cancelled</SelectItem>
                            </SelectContent>
                        </Select>
                        <Select value={perPage} onValueChange={setPerPage}>
                            <SelectTrigger className="w-[110px]">
                                <SelectValue placeholder="Show 10" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="10">Show 10</SelectItem>
                                <SelectItem value="20">Show 20</SelectItem>
                                <SelectItem value="50">Show 50</SelectItem>
                                <SelectItem value="100">Show 100</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <Card className="border-sidebar-border/70 dark:border-sidebar-border shadow-sm">
                    <CardHeader>
                        <CardTitle>All Transactions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {isLoading ? (
                            <div className="space-y-4">
                                {[...Array(5)].map((_, i) => (
                                    <div key={i} className="flex items-center gap-4">
                                        <div className="h-10 w-10 rounded-full bg-muted animate-pulse" />
                                        <div className="space-y-2 flex-1">
                                            <div className="h-4 w-1/4 bg-muted animate-pulse rounded" />
                                            <div className="h-3 w-1/5 bg-muted animate-pulse rounded" />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : transactions.length > 0 ? (
                            <div className="space-y-4">
                                {transactions.map((tx) => (
                                    <div key={tx.id} className="flex items-center justify-between border-b border-border/50 pb-4 last:border-0 last:pb-0 group">
                                        <div className="flex items-center gap-4">
                                            <div className={`p-2 rounded-full ${
                                                tx.type === 'income' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                                tx.type === 'expense' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' :
                                                'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'
                                            }`}>
                                                {tx.type === 'income' ? <TrendingUp size={16} /> : 
                                                 tx.type === 'expense' ? <TrendingDown size={16} /> : 
                                                 <ArrowRightLeft size={16} />}
                                            </div>
                                            <div>
                                                <p className="text-sm font-medium leading-none">{tx.description || (tx.category?.name ?? 'Uncategorized')}</p>
                                                <p className="text-xs text-muted-foreground mt-1">
                                                    {format(new Date(tx.transaction_date), 'MMM dd, yyyy')} • {tx.account?.name}
                                                </p>
                                                {tx.status === 'pending' && (
                                                    <Badge variant="outline" className="mt-1 text-[10px] py-0 px-1.5 bg-amber-500/10 text-amber-500 border-amber-500/30">
                                                        Pending Draft
                                                    </Badge>
                                                )}
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            {tx.status === 'pending' && (
                                                <Button 
                                                    size="sm" 
                                                    variant="outline" 
                                                    className="h-8 text-xs border-amber-500/30 hover:bg-amber-500/10 hover:text-amber-600"
                                                    onClick={() => {
                                                        setConfirmTransaction(tx);
                                                        setConfirmAmount(Number(tx.amount).toString());
                                                    }}
                                                >
                                                    <CheckCircle2 className="h-3.5 w-3.5 mr-1" />
                                                    Confirm
                                                </Button>
                                            )}
                                            <div className={`font-medium ${
                                                tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 
                                                tx.type === 'expense' ? 'text-red-600 dark:text-red-400' : ''
                                            }`}>
                                                {tx.type === 'income' ? '+' : tx.type === 'expense' ? '-' : ''}
                                                <AmountDisplay amount={Number(tx.amount)} showSymbol={false} />
                                            </div>
                                            <Button 
                                                variant="ghost" 
                                                size="icon" 
                                                className="opacity-0 group-hover:opacity-100 transition-opacity"
                                                onClick={() => router.visit(`/transactions/${tx.id}/edit`)}
                                            >
                                                <Pencil className="h-4 w-4 text-muted-foreground" />
                                            </Button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8">
                                <p className="text-muted-foreground">No transactions found.</p>
                            </div>
                        )}
                        
                        {/* Pagination UI */}
                        {!isLoading && meta.last_page > 1 && (
                            <div className="flex items-center justify-between mt-6 pt-4 border-t border-border/50">
                                <div className="text-sm text-muted-foreground">
                                    Showing page {meta.current_page} of {meta.last_page} ({meta.total} total transactions)
                                </div>
                                <div className="flex gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={meta.current_page === 1}
                                        onClick={() => setPage(p => Math.max(1, p - 1))}
                                    >
                                        Previous
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        disabled={meta.current_page === meta.last_page}
                                        onClick={() => setPage(p => Math.min(meta.last_page, p + 1))}
                                    >
                                        Next
                                    </Button>
                                </div>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Confirm Transaction Dialog */}
            <Dialog open={confirmTransaction !== null} onOpenChange={(open) => !open && setConfirmTransaction(null)}>
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Confirm Transaction</DialogTitle>
                        <DialogDescription>
                            Confirm or adjust the final amount before marking this transaction as completed.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-4 space-y-2">
                        <Label htmlFor="confirmAmount">Actual Amount</Label>
                        <Input 
                            id="confirmAmount" 
                            type="number" 
                            step="0.01" 
                            value={confirmAmount} 
                            onChange={(e) => setConfirmAmount(e.target.value)} 
                            placeholder="Enter the actual amount"
                        />
                    </div>
                    <DialogFooter className="mt-2 gap-2 sm:gap-0">
                        <Button variant="outline" onClick={() => setConfirmTransaction(null)}>Cancel</Button>
                        <Button variant="default" onClick={handleConfirmSubmit}>Confirm Transaction</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

import AppLayout from '@/layouts/app-layout';

TransactionsIndex.layout = (page: any) => (
    <AppLayout breadcrumbs={[{ title: 'Transactions', href: '/transactions' }]}>
        {page}
    </AppLayout>
);
