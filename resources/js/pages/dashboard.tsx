import { Head, Link } from '@inertiajs/react';
import { dashboard } from '@/routes';
import { useEffect, useState } from 'react';
import axios from '@/lib/axios';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Wallet, TrendingUp, TrendingDown, Activity, ArrowRightLeft, Plus, ArrowDown, ArrowUp, CheckCircle2, Eye, EyeOff } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { format } from 'date-fns';
import AmountDisplay from '@/components/AmountDisplay';
import { toast } from 'sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function Dashboard() {
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [todayIncome, setTodayIncome] = useState(0);
    const [todayExpense, setTodayExpense] = useState(0);
    const [isLoading, setIsLoading] = useState(true);
    const [accountSortOrder, setAccountSortOrder] = useState<'desc' | 'asc'>('desc');
    const [confirmTransaction, setConfirmTransaction] = useState<Transaction | null>(null);
    const [confirmAmount, setConfirmAmount] = useState<string>('');
    const [isNetWorthHidden, setIsNetWorthHidden] = useState(false);
    const [hasLoaded, setHasLoaded] = useState(false);

    useEffect(() => {
        if (typeof window !== 'undefined') {
            setIsNetWorthHidden(localStorage.getItem('hideNetWorth') === 'true');
            setHasLoaded(true);
        }
    }, []);

    useEffect(() => {
        if (hasLoaded && typeof window !== 'undefined') {
            localStorage.setItem('hideNetWorth', isNetWorthHidden.toString());
        }
    }, [isNetWorthHidden, hasLoaded]);

    const handleConfirmSubmit = async () => {
        if (!confirmTransaction) return;
        try {
            await axios.put(`/transactions/${confirmTransaction.id}`, {
                ...confirmTransaction,
                amount: confirmAmount ? Number(confirmAmount) : confirmTransaction.amount,
                status: 'completed'
            });
            toast.success('Transaction confirmed!');
            window.location.reload();
        } catch (error) {
            console.error("Failed to confirm transaction", error);
            toast.error('Failed to confirm transaction.');
        }
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                const today = format(new Date(), 'yyyy-MM-dd');
                const [accountsRes, transactionsRes, summaryRes] = await Promise.all([
                    axios.get<{ data: Account[] }>('/accounts'),
                    axios.get<{ data: Transaction[] }>(`/transactions?start_date=${today}&end_date=${today}`),
                    axios.get<{ data: { income: string, expense: string } }>(`/transactions/summary?start_date=${today}&end_date=${today}&exclude_reference_prefix=REC-IN-`)
                ]);
                setAccounts(accountsRes.data.data);
                
                setTodayIncome(Number(summaryRes.data.data.income));
                setTodayExpense(Number(summaryRes.data.data.expense));
                
                const displayTx = transactionsRes.data.data.filter((t: Transaction) => !t.reference?.startsWith('REC-IN-'));
                setTransactions(displayTx);
            } catch (error) {
                console.error("Failed to fetch dashboard data", error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchData();
    }, []);

    const totalBalance = accounts.reduce((sum, account) => sum + Number(account.current_balance), 0);

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="grid gap-4 md:grid-cols-3">
                    <Card className="border-sidebar-border/70 dark:border-emerald-900/50 bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950/40 dark:to-zinc-900/80 shadow-sm dark:shadow-emerald-900/20 hover:shadow-md transition-all">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Net Worth</CardTitle>
                            <div className="flex items-center gap-2">
                                <Button 
                                    variant="ghost" 
                                    size="icon" 
                                    className="h-6 w-6 text-muted-foreground hover:text-foreground"
                                    onClick={() => setIsNetWorthHidden(!isNetWorthHidden)}
                                    title={isNetWorthHidden ? "Show balance" : "Hide balance"}
                                >
                                    {isNetWorthHidden ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                                </Button>
                                <Wallet className="h-4 w-4 text-emerald-600 dark:text-emerald-400" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">
                                {isNetWorthHidden ? '•••••••' : <AmountDisplay amount={totalBalance} />}
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Across {accounts.length} active accounts
                            </p>
                        </CardContent>
                    </Card>

                    <Card className="border-sidebar-border/70 dark:border-emerald-900/50 bg-gradient-to-br from-emerald-50 to-white dark:from-emerald-950/40 dark:to-zinc-900/80 shadow-sm dark:shadow-emerald-900/20 hover:shadow-md transition-all">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Today's Income</CardTitle>
                            <TrendingUp className="h-4 w-4 text-emerald-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                + <AmountDisplay amount={todayIncome} showSymbol={false} />
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                {format(new Date(), 'dd MMMM yyyy')}
                            </p>
                        </CardContent>
                    </Card>

                    <Card className="border-sidebar-border/70 dark:border-red-900/50 bg-gradient-to-br from-red-50 to-white dark:from-red-950/40 dark:to-zinc-900/80 shadow-sm dark:shadow-red-900/20 hover:shadow-md transition-all">
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Today's Expenses</CardTitle>
                            <TrendingDown className="h-4 w-4 text-red-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600 dark:text-red-400">
                                - <AmountDisplay amount={todayExpense} showSymbol={false} />
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">
                                {format(new Date(), 'dd MMMM yyyy')}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                    <Card className="md:col-span-1 lg:col-span-4 border-sidebar-border/70 dark:border-sidebar-border/50 dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-all">
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
                                                    {tx.status === 'pending' && (
                                                        <Badge variant="outline" className="mt-1 text-[10px] py-0 px-1.5 bg-amber-500/10 text-amber-500 border-amber-500/30">
                                                            Pending Draft
                                                        </Badge>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="text-right flex items-center gap-3">
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

                    <Card className="md:col-span-1 lg:col-span-3 border-sidebar-border/70 dark:border-sidebar-border/50 dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-all">
                        <CardHeader className="flex flex-row items-center justify-between pb-2">
                            <CardTitle>Accounts Overview</CardTitle>
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => setAccountSortOrder(prev => prev === 'desc' ? 'asc' : 'desc')}
                                className="h-8 text-muted-foreground"
                            >
                                {accountSortOrder === 'desc' ? (
                                    <>Highest <ArrowDown className="ml-1 h-4 w-4" /></>
                                ) : (
                                    <>Lowest <ArrowUp className="ml-1 h-4 w-4" /></>
                                )}
                            </Button>
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
                                    {[...accounts]
                                        .sort((a, b) => 
                                            accountSortOrder === 'desc' 
                                                ? Number(b.current_balance) - Number(a.current_balance)
                                                : Number(a.current_balance) - Number(b.current_balance)
                                        )
                                        .slice(0, 5)
                                        .map(account => (
                                        <div key={account.id} className="flex items-center justify-between border-b border-border/50 pb-4 last:border-0 last:pb-0">
                                            <div>
                                                <p className="font-medium">{account.name}</p>
                                                <p className="text-xs text-muted-foreground capitalize">{account.type.replace('_', ' ')}</p>
                                            </div>
                                            <div className="font-semibold">
                                                <AmountDisplay amount={Number(account.current_balance)} />
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

            {/* Floating Action Button */}
            <Link 
                href="/transactions/create" 
                className="group fixed bottom-6 right-6 md:bottom-8 md:right-8 z-50 flex h-12 md:h-14 items-center rounded-full bg-emerald-600 text-white font-medium shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-700/40 transition-all duration-300 hover:-translate-y-1 focus:outline-none overflow-hidden"
            >
                <div className="flex h-12 w-12 md:h-14 md:w-14 shrink-0 items-center justify-center">
                    <Plus className="h-6 w-6 transition-transform duration-300 group-hover:rotate-90" />
                </div>
                <div className="max-w-0 opacity-0 transition-all duration-300 ease-in-out group-hover:max-w-[200px] group-hover:opacity-100 group-hover:pr-6 md:group-hover:pr-8 whitespace-nowrap overflow-hidden flex items-center h-full">
                    <span>Add Transaction</span>
                </div>
            </Link>

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

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
