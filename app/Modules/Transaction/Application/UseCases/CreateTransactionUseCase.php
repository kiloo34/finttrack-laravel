<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTOs\CreateTransactionInput;
use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use App\Modules\Transaction\Domain\Entities\Transaction;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\ValueObjects\TransactionStatus;
use Illuminate\Support\Facades\DB;

final class CreateTransactionUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function execute(CreateTransactionInput $input): TransactionOutput
    {
        return DB::transaction(function () use ($input) {
            $transaction = Transaction::create(
                accountId: $input->accountId,
                categoryId: $input->categoryId,
                type: $input->type,
                amount: $input->amount,
                currency: $input->currency,
                transactionDate: $input->transactionDate,
                description: $input->description,
                reference: $input->reference,
                status: TransactionStatus::from($input->status),
            );

            $saved = $this->transactionRepository->save($transaction);

            return TransactionOutput::fromDomain($saved);
        });
    }
}
