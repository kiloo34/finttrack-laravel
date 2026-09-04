<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\TransactionSummaryOutput;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;

final class GetTransactionSummaryUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function execute(int $userId, array $filters): TransactionSummaryOutput
    {
        $result = $this->transactionRepository->getSummaryForUser($userId, $filters);

        return new TransactionSummaryOutput(
            income: $result['income'],
            expense: $result['expense'],
        );
    }
}
