<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

final class ProcessRecurringTransactionsUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $recurringRepository,
        private readonly ProcessSingleRecurringTransactionUseCase $processSingleUseCase,
    ) {}

    public function execute(): int
    {
        $today = Carbon::today();
        $dueTransactions = $this->recurringRepository->getActiveDueTransactions($today);
        $processedCount = 0;

        foreach ($dueTransactions as $recurring) {
            try {
                $this->processSingleUseCase->executeForEntity($recurring);
                $processedCount++;
            } catch (\Exception $e) {
                Log::error('Failed to process recurring transaction ID '.$recurring->getId().': '.$e->getMessage());
            }
        }

        return $processedCount;
    }
}
