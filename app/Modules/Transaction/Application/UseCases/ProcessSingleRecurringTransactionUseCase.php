<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Transaction\Application\DTOs\CreateTransactionInput;
use App\Modules\Transaction\Domain\Entities\RecurringTransaction;
use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Services\HolidayServiceInterface;
use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use App\Notifications\RecurringTransactionExecutedNotification;
use Illuminate\Support\Facades\DB;

final class ProcessSingleRecurringTransactionUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $recurringRepository,
        private readonly CreateTransactionUseCase $createTransactionUseCase,
        private readonly HolidayServiceInterface $holidayService,
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(int $recurringId, ?string $overrideAmount = null): void
    {
        $recurring = $this->recurringRepository->findById($recurringId);

        if (! $recurring) {
            throw new \InvalidArgumentException('Recurring transaction not found');
        }

        $this->executeForEntity($recurring, $overrideAmount);
    }

    public function executeForEntity(RecurringTransaction $recurring, ?string $overrideAmount = null): void
    {
        $amountToCheck = $overrideAmount ?? $recurring->getAmount();

        $exists = $this->transactionRepository->existsSimilarTransaction(
            $recurring->getAccountId(),
            $recurring->getType(),
            $amountToCheck,
            $recurring->getNextRunDate()
        );

        if ($exists) {
            // Advance the next run date but do not create the transaction
            $recurring->advanceNextRunDate($this->holidayService);
            $this->recurringRepository->save($recurring);

            throw new \LogicException('Transaction already exists for this date. Schedule skipped to next run date.');
        }

        DB::transaction(function () use ($recurring, $overrideAmount) {
            // Determine the status of the generated transaction
            $status = match ($recurring->getExecutionType()) {
                ExecutionType::AutoComplete => TransactionStatus::Completed->value,
                ExecutionType::Draft => TransactionStatus::Pending->value,
            };

            // Create the transaction
            $input = new CreateTransactionInput(
                accountId: $recurring->getAccountId(),
                categoryId: $recurring->getCategoryId(),
                type: $recurring->getType(),
                amount: $overrideAmount ?? $recurring->getAmount(),
                currency: $recurring->getCurrency(),
                transactionDate: $recurring->getNextRunDate(), // Use the scheduled date, not today
                description: $recurring->getDescription() ?? 'Recurring Transaction',
                reference: 'REC-'.$recurring->getId().'-'.time(),
                status: $status
            );

            $this->createTransactionUseCase->execute($input);

            if ($recurring->getType() === 'transfer' && $recurring->getDestinationAccountId()) {
                $incomeInput = new CreateTransactionInput(
                    accountId: $recurring->getDestinationAccountId(),
                    categoryId: $recurring->getCategoryId(),
                    type: 'income',
                    amount: $recurring->getAmount(),
                    currency: $recurring->getCurrency(),
                    transactionDate: $recurring->getNextRunDate(),
                    description: ($recurring->getDescription() ?? 'Recurring Transfer').' (Received)',
                    reference: 'REC-IN-'.$recurring->getId().'-'.time(),
                    status: $status
                );
                $this->createTransactionUseCase->execute($incomeInput);
            }

            // Advance the next run date
            $recurring->advanceNextRunDate($this->holidayService);
            $this->recurringRepository->save($recurring);

            // Send notification
            $account = $this->accountRepository->findById(AccountId::fromString($recurring->getAccountId()));
            if ($account) {
                $user = UserModel::find($account->getUserId());
                if ($user) {
                    $user->notify(new RecurringTransactionExecutedNotification(
                        title: 'Recurring Transaction Executed',
                        message: $recurring->getDescription() ?? 'Your scheduled transaction was successfully processed.',
                        amount: $overrideAmount ?? $recurring->getAmount(),
                        type: $recurring->getType(),
                        recurringId: $recurring->getId()
                    ));
                }
            }
        });
    }
}
