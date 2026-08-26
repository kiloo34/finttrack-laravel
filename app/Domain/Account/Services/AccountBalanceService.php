<?php

namespace App\Domain\Account\Services;

use App\Domain\Account\Models\Account;
use Illuminate\Support\Facades\DB;

class AccountBalanceService
{
    /**
     * Calculate the current balance for an account.
     *
     * Balance = initial_balance + sum(credits) - sum(debits)
     *
     * This derives balance from the immutable ledger.
     * Do NOT use accounts.current_balance as the sole source of truth.
     */
    public function calculate(Account $account): string
    {
        $credits = DB::table('ledger_entries')
            ->where('account_id', $account->id)
            ->where('direction', 'credit')
            ->sum('amount');

        $debits = DB::table('ledger_entries')
            ->where('account_id', $account->id)
            ->where('direction', 'debit')
            ->sum('amount');

        $balance = bcadd(
            (string) $account->initial_balance,
            bcsub((string) $credits, (string) $debits, 2),
            2
        );

        return $balance;
    }

    /**
     * Calculate the total balance across all of a user's active accounts.
     */
    public function totalForUser(int $userId): string
    {
        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $total = '0.00';

        foreach ($accounts as $account) {
            $total = bcadd($total, $this->calculate($account), 2);
        }

        return $total;
    }
}
