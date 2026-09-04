<?php

namespace App\Modules\Account\Infrastructure\Persistence\Eloquent\Mappers;

use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;

final class AccountMapper
{
    /**
     * Convert an Eloquent model to a pure Domain Entity.
     */
    public static function toDomain(AccountModel $model): Account
    {
        return Account::reconstitute(
            id: AccountId::fromString((string) $model->id),
            userId: (int) $model->user_id,
            name: (string) $model->name,
            type: $model->type,
            currency: (string) $model->currency,
            accountNumber: $model->account_number ? (string) $model->account_number : null,
            providerId: $model->provider_id ? (int) $model->provider_id : null,
            providerName: $model->provider ? (string) $model->provider->name : null,
            initialBalance: (string) $model->initial_balance,
            isActive: (bool) $model->is_active,
            syncFrequency: (string) ($model->sync_frequency ?? 'manual'),
            createdAt: new \DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new \DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
        );
    }

    /**
     * Convert a Domain Entity to an array for Eloquent persistence.
     *
     * @return array<string, mixed>
     */
    public static function toPersistence(Account $account): array
    {
        return [
            'user_id' => $account->getUserId(),
            'name' => $account->getName(),
            'type' => $account->getType(),
            'currency' => $account->getCurrency(),
            'account_number' => $account->getAccountNumber(),
            'provider_id' => $account->getProviderId(),
            'initial_balance' => $account->getInitialBalance(),
            'is_active' => $account->isActive(),
            'sync_frequency' => $account->getSyncFrequency(),
        ];
    }
}
