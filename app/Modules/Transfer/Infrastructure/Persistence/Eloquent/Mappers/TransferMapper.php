<?php

namespace App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Transfer\Domain\Entities\Transfer;
use App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Models\TransferModel;

final class TransferMapper
{
    public static function toDomain(TransferModel $model): Transfer
    {
        return Transfer::reconstitute(
            id: (int) $model->id,
            fromAccountId: (int) $model->from_account_id,
            toAccountId: (int) $model->to_account_id,
            amount: (string) $model->amount,
            currency: (string) $model->currency,
            transactionDate: new \DateTimeImmutable($model->transaction_date->toDateString()),
            description: $model->description,
            createdAt: new \DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new \DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPersistence(Transfer $transfer): array
    {
        return [
            'from_account_id' => $transfer->getFromAccountId(),
            'to_account_id' => $transfer->getToAccountId(),
            'amount' => abs((float) $transfer->getAmount()),
            'currency' => $transfer->getCurrency(),
            'transaction_date' => $transfer->getTransactionDate()->format('Y-m-d'),
            'description' => $transfer->getDescription(),
        ];
    }
}
