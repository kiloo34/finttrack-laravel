<?php

namespace App\Modules\Account\Domain\Repositories;

use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\ValueObjects\AccountId;

/**
 * Port / Repository Interface (Pure PHP).
 * Infrastructure layer implements this.
 */
interface AccountRepositoryInterface
{
    public function save(Account $account): void;

    public function findById(AccountId $id): ?Account;

    /** @return Account[] */
    public function findByUserId(int $userId): array;

    public function delete(AccountId $id): void;
}
