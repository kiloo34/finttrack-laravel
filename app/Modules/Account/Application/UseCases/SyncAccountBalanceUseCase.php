<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Infrastructure\Services\AccountBalanceService;
use App\Modules\Transaction\Application\DTOs\CreateTransactionInput;
use App\Modules\Transaction\Application\UseCases\CreateTransactionUseCase;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

final class SyncAccountBalanceUseCase
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly AccountBalanceService $accountBalanceService,
        private readonly CreateTransactionUseCase $createTransactionUseCase,
    ) {}

    /**
     * @param  numeric-string  $actualBalance
     */
    public function execute(string $accountId, string $actualBalance, int $categoryId, ?\DateTimeInterface $syncDate = null): void
    {
        $date = $syncDate ? Carbon::instance($syncDate) : Carbon::today();

        // Validate date is between 25th to end of month OR 1st to 5th
        $day = $date->day;
        if (! ($day >= 25 || $day <= 5)) {
            throw ValidationException::withMessages([
                'sync' => 'Balance sync is only allowed between the 25th and the 5th of the month.',
            ]);
        }

        $account = $this->accountRepository->findById(AccountId::fromString($accountId));
        if (! $account) {
            throw new \InvalidArgumentException('Account not found');
        }

        // Calculate current system balance
        $systemBalance = $this->accountBalanceService->calculate(
            (int) $account->getId()->value(), 
            $account->getInitialBalance(), 
            $date
        );

        $discrepancy = bcsub($actualBalance, $systemBalance, 2);

        // If exact match, do nothing
        if ($discrepancy === '0.00' || $discrepancy === '0' || $discrepancy === '-0.00') {
            return;
        }

        $isExpense = bccomp($discrepancy, '0.00', 2) === -1; // -1 if discrepancy < 0

        $type = $isExpense ? 'expense' : 'income';
        $amount = $isExpense ? ltrim($discrepancy, '-') : $discrepancy;

        $description = $isExpense ? 'Balance Adjustment (Admin Fee / Missing Funds)' : 'Balance Adjustment (Interest / Extra Funds)';

        $input = new CreateTransactionInput(
            accountId: (int) $account->getId()->value(),
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            currency: $account->getCurrency(),
            transactionDate: new \DateTimeImmutable($date->toDateTimeString()),
            description: $description,
            reference: 'SYNC-'.$account->getId()->value().'-'.time(),
            status: TransactionStatus::Completed->value
        );

        $this->createTransactionUseCase->execute($input);
    }
}
