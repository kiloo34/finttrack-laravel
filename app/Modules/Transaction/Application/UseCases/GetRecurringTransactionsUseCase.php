<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\RecurringTransactionOutput;
use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;

final class GetRecurringTransactionsUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: RecurringTransactionOutput[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, array $filters, int $perPage = 15, int $page = 1): array
    {
        $result = $this->repository->paginateForUser($userId, $filters, $perPage, $page);

        return [
            'data' => array_map(fn ($domain) => RecurringTransactionOutput::fromDomain($domain), $result['data']),
            'total' => $result['total'],
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
        ];
    }
}
