<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use App\Modules\Transaction\Application\DTOs\UpdateTransactionInput;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
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

            if ($input->status === 'completed') {
                $transaction->updateStatus(TransactionStatus::Completed);
            } elseif ($input->status === 'pending') {
                $transaction->updateStatus(TransactionStatus::Pending);
            }

            $saved = $this->transactionRepository->save($transaction);

            // Auto-sync counterpart transaction if it's a recurring transfer
            $ref = $transaction->getReference();
            if ($ref && str_starts_with($ref, 'REC-')) {
                $isIncome = str_starts_with($ref, 'REC-IN-');
                $counterpartRef = $isIncome
                    ? str_replace('REC-IN-', 'REC-', $ref)
                    : str_replace('REC-', 'REC-IN-', $ref);

                // Update the counterpart in the database
                TransactionModel::query()->where('reference', $counterpartRef)->update([
                    'amount' => $transaction->getAmount(),
                    'status' => $transaction->getStatus()->value,
                ]);
            }

            return TransactionOutput::fromDomain($saved);
        });
    }
}
