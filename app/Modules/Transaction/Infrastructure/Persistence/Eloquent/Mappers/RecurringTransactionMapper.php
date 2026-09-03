<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Transaction\Domain\Entities\RecurringTransaction;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\RecurringTransactionModel;
use Carbon\CarbonImmutable;

final class RecurringTransactionMapper
{
    public static function toDomain(RecurringTransactionModel $model): RecurringTransaction
    {
        return RecurringTransaction::restore(
            id: $model->id,
            userId: $model->user_id,
            accountId: $model->account_id,
            destinationAccountId: $model->destination_account_id,
            categoryId: $model->category_id,
            type: $model->type,
            amount: (string) $model->amount,
            currency: $model->currency,
            description: $model->description,
            frequency: $model->frequency,
            executionType: $model->execution_type,
            nextRunDate: CarbonImmutable::parse($model->next_run_date),
            isActive: (bool) $model->is_active,
        );
    }

    /** @return array<string, mixed> */
    public static function toPersistence(RecurringTransaction $transaction): array
    {
        return [
            'user_id' => $transaction->getUserId(),
            'account_id' => $transaction->getAccountId(),
            'destination_account_id' => $transaction->getDestinationAccountId(),
            'category_id' => $transaction->getCategoryId(),
            'type' => $transaction->getType(),
            'amount' => $transaction->getAmount(),
            'currency' => $transaction->getCurrency(),
            'description' => $transaction->getDescription(),
            'frequency' => $transaction->getFrequency()->value,
            'execution_type' => $transaction->getExecutionType()->value,
            'next_run_date' => $transaction->getNextRunDate()->format('Y-m-d'),
            'is_active' => $transaction->isActive(),
        ];
    }
}
