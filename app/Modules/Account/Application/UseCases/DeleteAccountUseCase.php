<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use App\Modules\Account\Domain\ValueObjects\AccountId;
use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
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
    public function execute(AccountId $accountId, int $actingUserId, ?string $reason = null): void
    {
        $account = $this->accountRepository->findById($accountId);

        if (! $account) {
            throw new \DomainException('Account not found.');
        }

        if ($account->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to delete this account.');
        }

        // Persist reason before soft delete
        if ($reason) {
            AccountModel::where('id', $accountId->value())->update(['deleted_reason' => $reason]);
        }

        $this->accountRepository->delete($accountId);
    }
}
