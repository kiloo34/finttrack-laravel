import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import TransactionForm from '@/components/transactions/TransactionForm';

export default function CreateTransaction() {
    return (
        <>
            <Head title="New Transaction" />
            <div className="flex flex-1 flex-col gap-6 p-6">
                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h1 className="text-2xl font-bold tracking-tight">New transaction</h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Add a new expense, income, or transfer to your tracker.
                        </p>
                    </div>
                </div>

                <TransactionForm />
            </div>
        </>
    );
}

CreateTransaction.layout = (page: any) => (
    <AppLayout breadcrumbs={[
        { title: 'Transactions', href: '/transactions' },
        { title: 'New transaction', href: '/transactions/create' }
    ]}>
        {page}
    </AppLayout>
);
