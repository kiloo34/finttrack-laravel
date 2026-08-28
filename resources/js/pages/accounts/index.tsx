import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import axios from '@/lib/axios';
import { Account } from '@/types/finance';
import { Button } from '@/components/ui/button';
import { Plus, Wallet, Edit2 } from 'lucide-react';
import AccountModal from '@/components/accounts/AccountModal';

export default function AccountsIndex() {
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedAccount, setSelectedAccount] = useState<Account | null>(null);

    const fetchAccounts = async () => {
        setIsLoading(true);
        try {
            const response = await axios.get<{ data: Account[] }>('/accounts');
            setAccounts(response.data.data);
        } catch (error) {
            console.error('Failed to fetch accounts', error);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchAccounts();
    }, []);

    const handleOpenModal = (account?: Account) => {
        setSelectedAccount(account || null);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedAccount(null);
    };

    const totalBalance = accounts.reduce((acc, account) => acc + Number(account.current_balance), 0);

    return (
        <div className="flex h-full flex-1 flex-col gap-6 p-6">
            <Head title="Accounts" />
            
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold">Accounts</h1>
                    <p className="text-sm text-muted-foreground mt-1">Manage your financial accounts and wallets.</p>
                </div>
                <Button onClick={() => handleOpenModal()}>
                    <Plus className="mr-2 h-4 w-4" /> Add Account
                </Button>
            </div>

            <div className="grid gap-4 md:grid-cols-3">
                <div className="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
                    <div className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 className="tracking-tight text-sm font-medium">Total Balance</h3>
                        <Wallet className="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div className="text-2xl font-bold">
                        Rp {totalBalance.toLocaleString('id-ID')}
                    </div>
                </div>
            </div>

            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {isLoading ? (
                    <p className="text-sm text-muted-foreground">Loading accounts...</p>
                ) : accounts.length === 0 ? (
                    <div className="col-span-full py-12 text-center border rounded-xl bg-card border-dashed">
                        <Wallet className="mx-auto h-12 w-12 text-muted-foreground opacity-50 mb-4" />
                        <h3 className="text-lg font-medium">No accounts yet</h3>
                        <p className="text-sm text-muted-foreground mb-4">Add your first bank account or wallet to start tracking.</p>
                        <Button variant="outline" onClick={() => handleOpenModal()}>
                            Add Account
                        </Button>
                    </div>
                ) : (
                    accounts.map(account => (
                        <div key={account.id} className="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col">
                            <div className="p-6 flex flex-row items-center justify-between">
                                <div>
                                    <h3 className="font-medium text-lg leading-none">{account.name}</h3>
                                    <p className="text-sm text-muted-foreground mt-1 capitalize">{account.type}</p>
                                </div>
                                <Button variant="ghost" size="icon" onClick={() => handleOpenModal(account)}>
                                    <Edit2 className="h-4 w-4" />
                                </Button>
                            </div>
                            <div className="p-6 pt-0 mt-auto">
                                <div className="text-2xl font-bold">
                                    Rp {Number(account.current_balance).toLocaleString('id-ID')}
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            <AccountModal 
                isOpen={isModalOpen} 
                onClose={handleCloseModal} 
                onSuccess={fetchAccounts}
                account={selectedAccount}
            />
        </div>
    );
}
