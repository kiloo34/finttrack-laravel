<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\RecurringTransactionOutput;
use App\Modules\Transaction\Application\DTOs\UpdateRecurringTransactionInput;
use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final class UpdateRecurringTransactionUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $repository,
    ) {}

    public function execute(UpdateRecurringTransactionInput $input): RecurringTransactionOutput
    {
        $transaction = $this->repository->findById($input->id);

        if (! $transaction) {
            throw new ModelNotFoundException('Recurring transaction not found');
        }

        if ($transaction->getUserId() !== $input->userId) {
            throw new InvalidArgumentException('Unauthorized to update this recurring transaction');
        }

        $transaction->update(
            accountId: $input->accountId,
            destinationAccountId: $input->destinationAccountId,
            categoryId: $input->categoryId,
            type: $input->type,
            amount: $input->amount,
            currency: $input->currency,
            description: $input->description,
            frequency: $input->frequency,
            executionType: $input->executionType,
            nextRunDate: $input->nextRunDate,
            isActive: $input->isActive,
        );

        $saved = $this->repository->save($transaction);

        return RecurringTransactionOutput::fromDomain($saved);
    }
}
