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
     *
     * @return numeric-string
     */
    public function calculate(Account $account, ?\DateTimeInterface $asOfDate = null): string
    {
        $creditsQuery = DB::table('ledger_entries')
            ->where('account_id', $account->id)
            ->where('direction', 'credit');

        $debitsQuery = DB::table('ledger_entries')
            ->where('account_id', $account->id)
            ->where('direction', 'debit');

        if ($asOfDate) {
            $creditsQuery->where('created_at', '<=', $asOfDate);
            $debitsQuery->where('created_at', '<=', $asOfDate);
        }

        $credits = $creditsQuery->sum('amount');
        $debits = $debitsQuery->sum('amount');

        /** @var numeric-string $initial */
        $initial = (string) $account->initial_balance;

        /** @var numeric-string $diff */
        $diff = bcsub((string) $credits, (string) $debits, 2);

        $balance = bcadd($initial, $diff, 2);

        /** @var numeric-string $balance */
        return $balance;
    }

    /**
     * Calculate the total balance across all of a user's active accounts.
     *
     * @return numeric-string
     */
    public function totalForUser(int $userId): string
    {
        $accounts = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        /** @var numeric-string $total */
        $total = '0.00';

        foreach ($accounts as $account) {
            $total = bcadd($total, $this->calculate($account), 2);
        }

        /** @var numeric-string $total */
        return $total;
    }
}
