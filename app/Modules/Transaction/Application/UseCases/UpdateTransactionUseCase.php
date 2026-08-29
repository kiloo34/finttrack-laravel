<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use App\Modules\Transaction\Application\DTOs\UpdateTransactionInput;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

final class UpdateTransactionUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    /**
     * @throws \DomainException
     * @throws AuthorizationException
     */
    public function execute(UpdateTransactionInput $input, int $actingUserId): TransactionOutput
    {
        return DB::transaction(function () use ($input) {
            $transaction = $this->transactionRepository->findById($input->id);

            if (! $transaction) {
                throw new \DomainException('Transaction not found.');
            }

            $transaction->update(
                accountId: $input->accountId,
                categoryId: $input->categoryId,
                type: $input->type,
                amount: $input->amount,
                currency: $input->currency,
                transactionDate: $input->transactionDate,
                description: $input->description,
                reference: $input->reference,
            );

            $saved = $this->transactionRepository->save($transaction);

            return TransactionOutput::fromDomain($saved);
        });
    }
}
