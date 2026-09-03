<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use Illuminate\Auth\Access\AuthorizationException;

final class DeleteAccountUseCase
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    /**
     * @throws AuthorizationException
     * @throws \DomainException
     */
    public function execute(AccountId $accountId, int $actingUserId): void
    {
        $account = $this->accountRepository->findById($accountId);

        if (! $account) {
            throw new \DomainException('Account not found.');
        }

        if ($account->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to delete this account.');
        }

        $this->accountRepository->delete($accountId);
    }
}
