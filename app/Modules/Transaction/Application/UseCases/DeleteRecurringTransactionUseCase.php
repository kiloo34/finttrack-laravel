<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

final class DeleteRecurringTransactionUseCase
{
    public function __construct(
        private readonly RecurringTransactionRepositoryInterface $repository,
    ) {}

    public function execute(int $userId, int $id): void
    {
        $transaction = $this->repository->findById($id);

        if (! $transaction) {
            throw new ModelNotFoundException('Recurring transaction not found');
        }

        if ($transaction->getUserId() !== $userId) {
            throw new InvalidArgumentException('Unauthorized to delete this recurring transaction');
        }

        $this->repository->delete($id);
    }
}
