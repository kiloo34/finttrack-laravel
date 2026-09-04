export interface Account {
    id: number;
    name: string;
    type: string;
    currency: string;
    account_number?: string | null;
    provider_id?: number | null;
    provider_name?: string | null;
    initial_balance: number;
    current_balance: number;
    is_active: boolean;
    sync_frequency?: string;
    created_at: string;
    investment_assets?: InvestmentAsset[];
}

export interface InvestmentAsset {
    id: number;
    account_id: number;
    name: string;
    symbol?: string | null;
    type: string;
    lots?: number | null;
    units: number | string;
    average_buy_price: number | string;
    current_market_price: number | string;
    last_synced_at?: string | null;
}

export interface Category {
    id: number;
    name: string;
    type: 'income' | 'expense';
    parent_id?: number | null;
    icon?: string;
    color?: string;
}

export interface Transaction {
    id: number;
    account_id: number;
    category_id: number | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number;
    currency: string;
    transaction_date: string;
    description: string | null;
    status?: 'pending' | 'completed' | 'cancelled';
    reference?: string | null;
    created_at: string;
    
    // Relationships if loaded
    account?: Account;
    category?: Category;
}

export interface RecurringTransaction {
    id: number;
    user_id: number;
    account_id: number;
    destination_account_id: number | null;
    category_id: number | null;
    type: 'income' | 'expense' | 'transfer';
    amount: number | string;
    currency: string;
    description: string | null;
    frequency: 'daily' | 'weekly' | 'monthly' | 'yearly';
    execution_type: 'auto_complete' | 'draft';
    next_run_date: string;
    is_active: boolean;
}

export interface Transfer {
    id: number;
    from_account_id: number;
    to_account_id: number;
    amount: number;
    currency: string;
    transaction_date: string;
    description: string | null;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

export interface Provider {
    id: number;
    name: string;
    type: string;
}
