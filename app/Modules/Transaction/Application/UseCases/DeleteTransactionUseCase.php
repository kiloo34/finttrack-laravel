<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class DeleteTransactionUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(int $id): void
    {
        DB::transaction(function () use ($id) {
            $transaction = $this->transactionRepository->findById($id);

            if (! $transaction) {
                throw new \DomainException('Transaction not found.');
            }

            $this->transactionRepository->delete($id);
        });
    }
}
