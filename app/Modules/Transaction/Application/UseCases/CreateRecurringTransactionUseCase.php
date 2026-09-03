<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\CreateRecurringTransactionInput;
use App\Modules\Transaction\Application\DTOs\RecurringTransactionOutput;
use App\Modules\Transaction\Domain\Entities\RecurringTransaction;
use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;

final class CreateRecurringTransactionUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $repository,
    ) {}

    public function execute(CreateRecurringTransactionInput $input): RecurringTransactionOutput
    {
        $recurringTransaction = RecurringTransaction::create(
            userId: $input->userId,
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

        $saved = $this->repository->save($recurringTransaction);

        return RecurringTransactionOutput::fromDomain($saved);
    }
}
