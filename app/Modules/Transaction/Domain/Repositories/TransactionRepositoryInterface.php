<?php

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Entities\Transaction;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction;

    public function findById(int $id): ?Transaction;

    /**
     * @param  array<string, mixed>  $filters  Supports: account_id, category_id, type, start_date, end_date
     * @return array{data: Transaction[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, array $filters, int $perPage = 15, int $page = 1): array;

    public function delete(int $id): void;
}
