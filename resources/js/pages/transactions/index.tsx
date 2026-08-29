import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import axios from '@/lib/axios';
import { Transaction } from '@/types/finance';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Plus, ArrowRightLeft, TrendingUp, TrendingDown, Pencil } from 'lucide-react';
import { format } from 'date-fns';
import AmountDisplay from '@/components/AmountDisplay';

export default function TransactionsIndex() {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    const fetchTransactions = async () => {
        try {
            setIsLoading(true);
            const res = await axios.get<{ data: Transaction[] }>('/transactions');
            setTransactions(res.data.data);
        } catch (error) {
            console.error("Failed to fetch transactions", error);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchTransactions();
    }, []);

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
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
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
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

import AppLayout from '@/layouts/app-layout';

TransactionsIndex.layout = (page: any) => (
    <AppLayout breadcrumbs={[{ title: 'Transactions', href: '/transactions' }]}>
        {page}
    </AppLayout>
);
