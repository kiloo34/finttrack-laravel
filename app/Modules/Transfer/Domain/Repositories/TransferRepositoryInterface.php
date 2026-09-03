<?php

namespace App\Modules\Transfer\Domain\Repositories;

use App\Modules\Transfer\Domain\Entities\Transfer;

interface TransferRepositoryInterface
{
    public function save(Transfer $transfer): Transfer;

    public function findById(int $id): ?Transfer;

    /**
     * @return array{data: Transfer[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, int $perPage = 15, int $page = 1): array;
}
