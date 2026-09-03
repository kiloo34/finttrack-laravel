import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import TransactionForm from '@/components/transactions/TransactionForm';
import { Transaction } from '@/types/finance';
import axios from '@/lib/axios';
import { Loader2 } from 'lucide-react';

export default function EditTransaction({ id: propId }: { id?: string }) {
    // Route::inertia() does not pass route params as props,
    // so we extract the ID from the URL path as a fallback.
    const extractedId = propId || window.location.pathname.split('/').filter(Boolean).find((_, i, arr) => arr[i - 1] === 'transactions');
    const [transaction, setTransaction] = useState<Transaction | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchTransaction = async () => {
            try {
                const res = await axios.get<{ data: Transaction }>(`/transactions/${extractedId}`);
                setTransaction(res.data.data);
            } catch (err) {
                console.error("Failed to load transaction", err);
                setError('Failed to load transaction data.');
            } finally {
                setIsLoading(false);
            }
        };

        if (extractedId) {
            fetchTransaction();
        } else {
            setError('Transaction ID not found.');
            setIsLoading(false);
        }
    }, [extractedId]);

    return (
        <>
            <Head title="Edit Transaction" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">Edit transaction</h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Update the details of your transaction.
                        </p>
                    </div>
                </div>

                {isLoading ? (
                    <div className="flex h-64 items-center justify-center">
                        <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
                    </div>
                ) : error ? (
                    <div className="rounded-md bg-destructive/15 p-4 text-destructive">
                        {error}
                    </div>
                ) : transaction ? (
                    <TransactionForm initialData={transaction} />
                ) : (
                    <div className="text-muted-foreground">Transaction not found.</div>
                )}
            </div>
        </>
    );
}

EditTransaction.layout = (page: any) => (
    <AppLayout breadcrumbs={[
        { title: 'Transactions', href: '/transactions' },
        { title: 'Edit transaction', href: '#' }
    ]}>
        {page}
    </AppLayout>
);
