<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Transaction\Domain\Entities\Transaction;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;

final class TransactionMapper
{
    public static function toDomain(TransactionModel $model): Transaction
    {
        return Transaction::reconstitute(
            id: (int) $model->id,
            accountId: (int) $model->account_id,
            categoryId: $model->category_id ? (int) $model->category_id : null,
            type: $model->type,
            amount: (string) $model->amount,
            currency: (string) $model->currency,
            transactionDate: new \DateTimeImmutable($model->transaction_date->toDateString()),
            description: $model->description,
            reference: $model->reference,
            status: TransactionStatus::from($model->status),
            createdAt: new \DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new \DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
            attachmentUrl: $model->attachment_url,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPersistence(Transaction $transaction): array
    {
        return [
            'account_id' => $transaction->getAccountId(),
            'category_id' => $transaction->getCategoryId(),
            'type' => $transaction->getType(),
            'amount' => $transaction->getAmount(),
            'currency' => $transaction->getCurrency(),
            'transaction_date' => $transaction->getTransactionDate()->format('Y-m-d'),
            'description' => $transaction->getDescription(),
            'reference' => $transaction->getReference(),
            'status' => $transaction->getStatus()->value,
            'attachment_url' => $transaction->getAttachmentUrl(),
        ];
    }
}
