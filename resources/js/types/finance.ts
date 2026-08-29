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
    created_at: string;
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
    created_at: string;
    
    // Relationships if loaded
    account?: Account;
    category?: Category;
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
