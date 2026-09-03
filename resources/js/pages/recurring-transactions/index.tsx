import React, { useEffect, useState } from 'react';
import { Head } from '@inertiajs/react';
import { RecurringTransaction } from '@/types/finance';
import axios from '@/lib/axios';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { CalendarClock, Plus, ArrowRightLeft, TrendingUp, TrendingDown, Pencil, Trash2, Play } from 'lucide-react';
import AmountDisplay from '@/components/AmountDisplay';
import { Badge } from '@/components/ui/badge';
import { toast } from 'sonner';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import RecurringTransactionForm from '@/components/recurring-transactions/RecurringTransactionForm';
import { Account, Category } from '@/types/finance';

export default function RecurringTransactionsPage() {
    const [recurring, setRecurring] = useState<RecurringTransaction[]>([]);
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isFormOpen, setIsFormOpen] = useState(false);
    const [editingSchedule, setEditingSchedule] = useState<RecurringTransaction | null>(null);
    const [deleteConfirmId, setDeleteConfirmId] = useState<number | null>(null);
    const [executeSchedule, setExecuteSchedule] = useState<RecurringTransaction | null>(null);
    const [overrideAmount, setOverrideAmount] = useState<string>('');

    useEffect(() => {
        fetchRecurring();
    }, []);

    const fetchRecurring = async () => {
        try {
            const [recurringRes, accountsRes, categoriesRes] = await Promise.all([
                axios.get('/recurring-transactions'),
                axios.get('/accounts'),
                axios.get('/categories')
            ]);
            setRecurring(recurringRes.data.data);
            setAccounts(accountsRes.data.data);
            setCategories(categoriesRes.data.data);
        } catch (error) {
            console.error('Failed to fetch recurring transactions', error);
        } finally {
            setIsLoading(false);
        }
    };

    const getTypeIcon = (type: string) => {
        switch (type) {
            case 'income': return <TrendingUp className="h-4 w-4 text-emerald-500" />;
            case 'expense': return <TrendingDown className="h-4 w-4 text-red-500" />;
            case 'transfer': return <ArrowRightLeft className="h-4 w-4 text-blue-500" />;
            default: return null;
        }
    };

    const handleDelete = async () => {
        if (!deleteConfirmId) return;
        try {
            await axios.delete(`/recurring-transactions/${deleteConfirmId}`);
            toast.success('Schedule deleted', {
                description: 'The recurring schedule has been successfully removed.'
            });
            fetchRecurring();
            setDeleteConfirmId(null);
        } catch (error) {
            toast.error('Deletion failed', {
                description: 'An error occurred while trying to delete the schedule.'
            });
            console.error('Failed to delete schedule', error);
        }
    };

    const handleExecute = async () => {
        if (!executeSchedule) return;
        try {
            await axios.post(`/recurring-transactions/${executeSchedule.id}/execute`, {
                amount: overrideAmount ? Number(overrideAmount) : null
            });
            toast.success('Schedule executed', {
                description: 'The transaction has been created successfully. Check your transactions list.'
            });
            fetchRecurring();
            setExecuteSchedule(null);
            setOverrideAmount('');
        } catch (error: any) {
            if (error.response?.status === 409) {
                toast.warning('Schedule Skipped', {
                    description: error.response.data.message
                });
                fetchRecurring();
                setExecuteSchedule(null);
                setOverrideAmount('');
            } else {
                toast.error('Execution failed', {
                    description: 'An error occurred while trying to execute the schedule manually.'
                });
                console.error('Failed to execute schedule', error);
            }
        }
    };

    // Calculate today's date string for comparison
    const todayStr = new Date().toISOString().split('T')[0];

    return (
        <>
            <Head title="Recurring Transactions" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight text-foreground flex items-center gap-2">
                            <CalendarClock className="h-6 w-6 text-primary" />
                            Recurring Transactions
                        </h1>
                        <p className="text-muted-foreground mt-1 text-sm">
                            Manage your automated and scheduled transactions.
                        </p>
                    </div>
                    <Button onClick={() => { setEditingSchedule(null); setIsFormOpen(true); }}>
                        <Plus className="mr-2 h-4 w-4" /> New Schedule
                    </Button>
                </div>

                <Card className="border-sidebar-border/70 dark:border-sidebar-border/50">
                    <CardHeader>
                        <CardTitle>Active Schedules</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {isLoading ? (
                            <div className="text-center text-muted-foreground py-8">Loading schedules...</div>
                        ) : recurring.length === 0 ? (
                            <div className="text-center text-muted-foreground py-8">
                                <CalendarClock className="h-12 w-12 mx-auto text-muted-foreground/50 mb-3" />
                                <p>No recurring transactions found.</p>
                                <Button variant="link" className="mt-2" onClick={() => setIsFormOpen(true)}>Create your first schedule</Button>
                            </div>
                        ) : (
                            <div className="space-y-4">
                                {recurring.map((rt) => (
                                    <div key={rt.id} className="flex items-center justify-between p-4 border rounded-lg bg-card">
                                        <div className="flex items-center gap-4">
                                            <div className="h-10 w-10 rounded-full bg-secondary flex items-center justify-center">
                                                {getTypeIcon(rt.type)}
                                            </div>
                                            <div>
                                                <h4 className="font-semibold">{rt.description || 'Unnamed Schedule'}</h4>
                                                <div className="flex items-center gap-2 text-xs text-muted-foreground mt-1">
                                                    <span className="capitalize">{rt.frequency}</span>
                                                    <span>•</span>
                                                    <span>Next run: {rt.nextRunDate}</span>
                                                    {rt.nextRunDate < todayStr && (
                                                        <>
                                                            <span>•</span>
                                                            <Badge variant="outline" className="text-red-500 border-red-500/30 bg-red-500/10">Overdue</Badge>
                                                        </>
                                                    )}
                                                    {rt.nextRunDate === todayStr && (
                                                        <>
                                                            <span>•</span>
                                                            <Badge variant="outline" className="text-blue-500 border-blue-500/30 bg-blue-500/10">Due Today</Badge>
                                                        </>
                                                    )}
                                                    {rt.executionType === 'draft' && (
                                                        <>
                                                            <span>•</span>
                                                            <Badge variant="outline" className="text-amber-500 border-amber-500/30 bg-amber-500/10">Requires Confirmation</Badge>
                                                        </>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="text-right flex flex-col items-end gap-2">
                                            <div className="text-right">
                                                <AmountDisplay 
                                                    amount={Number(rt.amount)} 
                                                    type={rt.type as 'income' | 'expense' | 'transfer'} 
                                                    className="font-bold text-lg"
                                                />
                                                <div className="text-xs text-muted-foreground mt-1 flex justify-end items-center gap-1">
                                                    {rt.isActive ? (
                                                        <><div className="h-2 w-2 rounded-full bg-emerald-500"></div> Active</>
                                                    ) : (
                                                        <><div className="h-2 w-2 rounded-full bg-muted-foreground"></div> Paused</>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2 mt-2">
                                                {rt.isActive && rt.nextRunDate <= todayStr && (
                                                    <Button variant="default" size="sm" onClick={() => {
                                                        setExecuteSchedule(rt);
                                                        setOverrideAmount(Number(rt.amount).toString());
                                                    }} title="Execute manually now">
                                                        <Play className="h-3 w-3 mr-1" /> Execute Now
                                                    </Button>
                                                )}
                                                <Button variant="outline" size="sm" onClick={() => { setEditingSchedule(rt); setIsFormOpen(true); }}>
                                                    <Pencil className="h-3 w-3 mr-1" /> Edit
                                                </Button>
                                                <Button variant="outline" size="sm" className="text-red-500 hover:text-red-600 hover:bg-red-50" onClick={() => setDeleteConfirmId(rt.id)}>
                                                    <Trash2 className="h-3 w-3 mr-1" /> Delete
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>

            <Dialog open={isFormOpen} onOpenChange={setIsFormOpen}>
                <DialogContent className="sm:max-w-[600px] max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>{editingSchedule ? 'Edit Schedule' : 'Create Recurring Transaction'}</DialogTitle>
                    </DialogHeader>
                    {isFormOpen && (
                        <RecurringTransactionForm 
                            accounts={accounts} 
                            categories={categories} 
                            schedule={editingSchedule}
                            onSuccess={() => {
                                setIsFormOpen(false);
                                fetchRecurring();
                            }} 
                            onCancel={() => setIsFormOpen(false)} 
                        />
                    )}
                </DialogContent>
            </Dialog>

            {/* Delete Confirmation Dialog */}
            <Dialog open={deleteConfirmId !== null} onOpenChange={(open) => !open && setDeleteConfirmId(null)}>
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Delete Schedule</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete this schedule? This action will prevent future automated transactions, but past transactions will remain intact.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter className="mt-4 gap-2 sm:gap-0">
                        <Button variant="outline" onClick={() => setDeleteConfirmId(null)}>Cancel</Button>
                        <Button variant="destructive" onClick={handleDelete}>Yes, delete it</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Execute Confirmation Dialog */}
            <Dialog open={executeSchedule !== null} onOpenChange={(open) => !open && setExecuteSchedule(null)}>
                <DialogContent className="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Execute Manually</DialogTitle>
                        <DialogDescription>
                            Confirm or adjust the amount for this execution. A transaction will be created and the next run date will advance.
                        </DialogDescription>
                    </DialogHeader>
                    <div className="py-4 space-y-2">
                        {executeSchedule?.executionType === 'draft' ? (
                            <>
                                <Label htmlFor="overrideAmount">Actual Amount</Label>
                                <Input 
                                    id="overrideAmount" 
                                    type="number" 
                                    step="0.01" 
                                    value={overrideAmount} 
                                    onChange={(e) => setOverrideAmount(e.target.value)} 
                                    placeholder="Enter the actual amount"
                                />
                                <p className="text-xs text-muted-foreground">
                                    You can adjust the final amount before it is added to your pending list.
                                </p>
                            </>
                        ) : (
                            <div className="bg-muted/50 p-3 rounded-md text-sm">
                                <p>This auto-complete schedule will be executed immediately.</p>
                            </div>
                        )}
                    </div>
                    <DialogFooter className="mt-2 gap-2 sm:gap-0">
                        <Button variant="outline" onClick={() => setExecuteSchedule(null)}>Cancel</Button>
                        <Button variant="default" onClick={handleExecute}>Yes, execute now</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}

import AppLayout from '@/layouts/app-layout';

RecurringTransactionsPage.layout = (page: any) => (
    <AppLayout breadcrumbs={[{ title: 'Recurring Transactions', href: '/recurring-transactions' }]}>
        {page}
    </AppLayout>
);
