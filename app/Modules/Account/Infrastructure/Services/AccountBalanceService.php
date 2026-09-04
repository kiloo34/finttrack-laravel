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
        $cashBalance = $this->calculate($model->id, (string) $model->initial_balance, $asOfDate);
        
        if ($model->type !== 'investment') {
            return $cashBalance;
        }

        $portfolioValue = '0.00';
        foreach ($model->investmentAssets as $asset) {
            if ($asset->type === 'stock' && $asset->lots !== null) {
                // Stock: lots * 100 shares * price per share
                $shares = bcmul((string) $asset->lots, '100', 0);
                $assetValue = bcmul($shares, (string) $asset->current_market_price, 2);
            } else {
                // Mutual fund / others: units * NAB
                $assetValue = bcmul((string) $asset->units, (string) $asset->current_market_price, 2);
            }
            $portfolioValue = bcadd($portfolioValue, $assetValue, 2);
        }

        return bcadd($cashBalance, $portfolioValue, 2);
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
