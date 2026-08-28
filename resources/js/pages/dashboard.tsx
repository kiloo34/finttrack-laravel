import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes';
import { useEffect, useState } from 'react';
import axios from '@/lib/axios';
import { Account, Transaction } from '@/types/finance';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Wallet, TrendingUp, TrendingDown, Activity, ArrowRightLeft } from 'lucide-react';
import { format } from 'date-fns';

export default function Dashboard() {
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [accountsRes, transactionsRes] = await Promise.all([
                    axios.get<{ data: Account[] }>('/accounts'),
                    axios.get<{ data: Transaction[] }>('/transactions?limit=5')
                ]);
                setAccounts(accountsRes.data.data);
                setTransactions(transactionsRes.data.data.slice(0, 5));
            } catch (error) {
                console.error("Failed to fetch dashboard data", error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchData();
    }, []);

    const totalBalance = accounts.reduce((sum, account) => sum + Number(account.current_balance), 0);
    
    // Quick approximation for recent income/expense (in a real app this would be computed by backend over a time period)
    const recentIncome = transactions.filter(t => t.type === 'income').reduce((sum, t) => sum + Number(t.amount), 0);
    const recentExpense = transactions.filter(t => t.type === 'expense').reduce((sum, t) => sum + Number(t.amount), 0);

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950/20 dark:to-background shadow-sm">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Net Worth</CardTitle>
                            <Wallet className="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                Rp {totalBalance.toLocaleString('id-ID')}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Across {accounts.length} active accounts
                            </p>
                        </CardContent>
                    </Card>

                    <Card className="border-sidebar-border/70 dark:border-sidebar-border shadow-sm">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Recent Income</CardTitle>
                            <TrendingUp className="h-4 w-4 text-emerald-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                + Rp {recentIncome.toLocaleString('id-ID')}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                From recent transactions
                            </p>
                        </CardContent>
                    </Card>

                    <Card className="border-sidebar-border/70 dark:border-sidebar-border shadow-sm">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Recent Expenses</CardTitle>
                            <TrendingDown className="h-4 w-4 text-red-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-400">
                                - Rp {recentExpense.toLocaleString('id-ID')}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                From recent transactions
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <Card className="col-span-4 border-sidebar-border/70 dark:border-sidebar-border shadow-sm">
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Activity className="h-5 w-5" />
                                Recent Transactions
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {isLoading ? (
                                <div className="space-y-4">
                                    {[...Array(4)].map((_, i) => (
                                        <div key={i} className="flex items-center gap-4">
                                            <div className="h-10 w-10 rounded-full bg-muted animate-pulse" />
                                            <div className="space-y-2 flex-1">
                                                <div className="h-4 w-1/3 bg-muted animate-pulse rounded" />
                                                <div className="h-3 w-1/4 bg-muted animate-pulse rounded" />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : transactions.length > 0 ? (
                                <div className="space-y-6">
                                    {transactions.map((tx) => (
                                        <div key={tx.id} className="flex items-center justify-between">
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
                                            <div className={`font-medium ${
                                                tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 
                                                tx.type === 'expense' ? 'text-red-600 dark:text-red-400' : ''
                                            }`}>
                                                {tx.type === 'income' ? '+' : tx.type === 'expense' ? '-' : ''}
                                                Rp {Number(tx.amount).toLocaleString('id-ID')}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="flex flex-col items-center justify-center py-8 text-center">
                                    <div className="bg-muted rounded-full p-4 mb-4">
                                        <Wallet className="h-8 w-8 text-muted-foreground" />
                                    </div>
                                    <h3 className="text-lg font-medium">No transactions yet</h3>
                                    <p className="text-sm text-muted-foreground mt-1 max-w-sm">
                                        You haven't recorded any transactions. Start tracking your finances by adding one.
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="col-span-3 border-sidebar-border/70 dark:border-sidebar-border shadow-sm">
                        <CardHeader>
                            <CardTitle>Accounts Overview</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {isLoading ? (
                                <div className="space-y-4">
                                    {[...Array(3)].map((_, i) => (
                                        <div key={i} className="flex justify-between items-center">
                                            <div className="h-4 w-1/3 bg-muted animate-pulse rounded" />
                                            <div className="h-4 w-1/4 bg-muted animate-pulse rounded" />
                                        </div>
                                    ))}
                                </div>
                            ) : accounts.length > 0 ? (
                                <div className="space-y-4">
                                    {accounts.map(account => (
                                        <div key={account.id} className="flex items-center justify-between border-b border-border/50 pb-4 last:border-0 last:pb-0">
                                            <div>
                                                <p className="font-medium">{account.name}</p>
                                                <p className="text-xs text-muted-foreground capitalize">{account.type.replace('_', ' ')}</p>
                                            </div>
                                            <div className="font-semibold">
                                                Rp {Number(account.current_balance).toLocaleString('id-ID')}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground text-center py-4">No accounts found.</p>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
