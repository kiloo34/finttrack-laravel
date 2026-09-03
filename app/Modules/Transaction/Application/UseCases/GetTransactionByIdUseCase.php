<?php

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Transaction\Application\DTOs\TransactionOutput;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

final class GetTransactionByIdUseCase
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(int $id, int $actingUserId): ?TransactionOutput
    {
        $transaction = $this->transactionRepository->findById($id);

        if (! $transaction) {
            return null;
        }

        $account = $this->accountRepository->findById(AccountId::fromString($transaction->getAccountId()));

        if (! $account || $account->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to view this transaction.');
        }

        return TransactionOutput::fromDomain($transaction);
    }
}
