<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

final class GetTransactionsUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: TransactionOutput[], total: int, per_page: int, current_page: int}
     */
    public function execute(int $userId, array $filters, int $perPage = 15, int $page = 1): array
    {
        $result = $this->transactionRepository->paginateForUser($userId, $filters, $perPage, $page);

        return [
            ...$result,
            'data' => array_map(
                fn ($t) => TransactionOutput::fromDomain($t),
                $result['data'],
            ),
        ];
    }
}
