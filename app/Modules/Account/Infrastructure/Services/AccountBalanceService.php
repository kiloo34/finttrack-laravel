<?php

namespace App\Modules\Account\Infrastructure\Services;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use Illuminate\Support\Facades\DB;

/**
 * Infrastructure service — allowed to use DB/Eloquent.
 * Calculates account balance from the immutable ledger.
 */
final class AccountBalanceService
{
    /**
     * Calculate balance from an AccountModel instance.
     *
     * Balance = initial_balance + sum(credits) - sum(debits)
     *
     * @return numeric-string
     */
    public function calculate(int $accountId, string $initialBalance, ?\DateTimeInterface $asOfDate = null): string
    {
        $creditsQuery = DB::table('ledger_entries')
            ->where('account_id', $accountId)
            ->where('direction', 'credit');

        $debitsQuery = DB::table('ledger_entries')
            ->where('account_id', $accountId)
            ->where('direction', 'debit');

        if ($asOfDate) {
            $creditsQuery->where('created_at', '<=', $asOfDate);
            $debitsQuery->where('created_at', '<=', $asOfDate);
        }

        $credits = $creditsQuery->sum('amount');
        $debits = $debitsQuery->sum('amount');

        /** @var numeric-string $initial */
        $initial = $initialBalance;

        /** @var numeric-string $diff */
        $diff = bcsub((string) $credits, (string) $debits, 2);

        $balance = bcadd($initial, $diff, 2);

        /** @var numeric-string $balance */
        return $balance;
    }

    /**
     * Calculate balance from an AccountModel instance.
     *
     * @return numeric-string
     */
    public function calculateFromModel(AccountModel $model, ?\DateTimeInterface $asOfDate = null): string
    {
        return $this->calculate($model->id, (string) $model->initial_balance, $asOfDate);
    }

    /**
     * Calculate the total balance across all active accounts for a user.
     *
     * @return numeric-string
     */
    public function totalForUser(int $userId): string
    {
        $accounts = AccountModel::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        /** @var numeric-string $total */
        $total = '0.00';

        foreach ($accounts as $account) {
            $total = bcadd($total, $this->calculateFromModel($account), 2);
        }

        /** @var numeric-string $total */
        return $total;
    }
}
