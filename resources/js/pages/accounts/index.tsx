import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import axios from '@/lib/axios';
import { Account } from '@/types/finance';
import { Button } from '@/components/ui/button';
import { Plus, Wallet, Edit2, ChevronDown, Building2 } from 'lucide-react';
import AccountModal from '@/components/accounts/AccountModal';
import AmountDisplay from '@/components/AmountDisplay';

export default function AccountsIndex() {
    const [accounts, setAccounts] = useState<Account[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedAccount, setSelectedAccount] = useState<Account | null>(null);
    const [expandedProviders, setExpandedProviders] = useState<Record<string, boolean>>({});

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

    const toggleExpand = (providerName: string, e: React.MouseEvent) => {
        e.stopPropagation();
        setExpandedProviders(prev => ({
            ...prev,
            [providerName]: !prev[providerName]
        }));
    };

    const totalBalance = accounts.reduce((acc, account) => acc + Number(account.current_balance), 0);

    // Group accounts by provider
    const groupedAccounts = accounts.reduce((acc, account) => {
        const groupName = account.provider_name || account.name; // Fallback to account name if no provider
        if (!acc[groupName]) {
            acc[groupName] = {
                total: 0,
                accounts: []
            };
        }
        acc[groupName].total += Number(account.current_balance);
        acc[groupName].accounts.push(account);
        return acc;
    }, {} as Record<string, { total: number, accounts: Account[] }>);

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
                        <AmountDisplay amount={totalBalance} />
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
                    Object.entries(groupedAccounts).map(([providerName, group]) => {
                        const isExpanded = !!expandedProviders[providerName];
                        const hasMultiple = group.accounts.length > 1;

                        return (
                            <div key={providerName} className="rounded-xl border bg-card text-card-foreground shadow-sm flex flex-col overflow-hidden">
                                <div 
                                    className={`p-6 flex flex-row items-center justify-between transition-colors ${hasMultiple ? 'cursor-pointer hover:bg-muted/50' : ''}`}
                                    onClick={(e) => hasMultiple && toggleExpand(providerName, e)}
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="p-2 rounded-lg bg-blue-50 text-blue-500">
                                            <Building2 className="h-5 w-5" />
                                        </div>
                                        <div>
                                            <h3 className="font-medium text-lg leading-none">{providerName}</h3>
                                            <p className="text-sm text-muted-foreground mt-1">
                                                {group.accounts.length} {group.accounts.length === 1 ? 'Account' : 'Accounts'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        {!hasMultiple && (
                                            <Button variant="ghost" size="icon" onClick={(e) => { e.stopPropagation(); handleOpenModal(group.accounts[0]); }}>
                                                <Edit2 className="h-4 w-4" />
                                            </Button>
                                        )}
                                        {hasMultiple && (
                                            <Button variant="ghost" size="icon" className="text-muted-foreground">
                                                <ChevronDown className={`h-4 w-4 transition-transform duration-200 ${isExpanded ? "rotate-180" : ""}`} />
                                            </Button>
                                        )}
                                    </div>
                                </div>
                                <div className="px-6 pb-6 pt-0">
                                    <div className="text-2xl font-bold">
                                        <AmountDisplay amount={group.total} />
                                    </div>
                                </div>
                                
                                {/* Render sub accounts if expanded */}
                                {hasMultiple && isExpanded && (
                                    <div className="bg-muted/30 border-t animate-in slide-in-from-top-2 fade-in duration-200">
                                        {group.accounts.map((account, index) => (
                                            <div key={account.id} className={`flex items-center justify-between p-4 px-6 ${index !== group.accounts.length - 1 ? 'border-b' : ''}`}>
                                                <div>
                                                    <h4 className="font-medium text-sm">{account.name}</h4>
                                                    <p className="text-xs text-muted-foreground">
                                                        {account.type} {account.account_number ? `• ${account.account_number}` : ''}
                                                    </p>
                                                    <div className="text-sm font-semibold mt-1">
                                                        <AmountDisplay amount={Number(account.current_balance)} />
                                                    </div>
                                                </div>
                                                <Button variant="ghost" size="icon" className="h-8 w-8" onClick={(e) => { e.stopPropagation(); handleOpenModal(account); }}>
                                                    <Edit2 className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        );
                    })
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
