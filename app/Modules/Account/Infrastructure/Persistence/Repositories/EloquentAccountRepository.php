<?php

namespace App\Modules\Account\Infrastructure\Persistence\Repositories;

use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Mappers\AccountMapper;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;

final class EloquentAccountRepository implements AccountRepositoryInterface
{
    public function save(Account $account): void
    {
        AccountModel::query()->updateOrCreate(
            ['id' => $account->getId()->value()],
            AccountMapper::toPersistence($account),
        );
    }

    public function findById(AccountId $id): ?Account
    {
        $model = AccountModel::query()->with('provider')->find($id->value());

        return $model ? AccountMapper::toDomain($model) : null;
    }

    /** @return Account[] */
    public function findByUserId(int $userId): array
    {
        return AccountModel::query()
            ->with('provider')
            ->where('user_id', $userId)
            ->get()
            ->map(fn (AccountModel $model) => AccountMapper::toDomain($model))
            ->all();
    }

    public function delete(AccountId $id): void
    {
        AccountModel::query()->where('id', $id->value())->delete();
    }
}
