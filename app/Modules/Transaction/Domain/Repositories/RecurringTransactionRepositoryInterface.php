<?php

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Entities\RecurringTransaction;

interface RecurringTransactionRepositoryInterface
{
    public function save(RecurringTransaction $transaction): RecurringTransaction;

    public function findById(int $id): ?RecurringTransaction;

    /**
     * @return RecurringTransaction[]
     */
    public function getActiveDueTransactions(\DateTimeInterface $date): array;

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: RecurringTransaction[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, array $filters, int $perPage = 15, int $page = 1): array;

    public function delete(int $id): void;
}
