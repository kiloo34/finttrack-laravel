<?php

namespace App\Modules\Transfer\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Transfer\Application\DTOs\TransferOutput;
use App\Modules\Transfer\Domain\Repositories\TransferRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

final class GetTransferByIdUseCase
{
    public function __construct(
        private readonly TransferRepositoryInterface $transferRepository,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(int $id, int $actingUserId): ?TransferOutput
    {
        $transfer = $this->transferRepository->findById($id);

        if (! $transfer) {
            return null;
        }

        $sourceAccount = $this->accountRepository->findById(AccountId::fromString($transfer->getFromAccountId()));

        if (! $sourceAccount || $sourceAccount->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to view this transfer.');
        }

        return TransferOutput::fromDomain($transfer);
    }
}
