<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Application\DTOs\AccountOutput;
use App\Modules\Account\Application\DTOs\UpdateAccountInput;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

final class UpdateAccountUseCase
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    /**
     * @throws AuthorizationException
     * @throws \DomainException
     */
    public function execute(UpdateAccountInput $input, int $actingUserId): AccountOutput
    {
        $account = $this->accountRepository->findById($input->id);

        if (! $account) {
            throw new \DomainException('Account not found.');
        }

        if ($account->getUserId() !== $actingUserId) {
            throw new AuthorizationException('You are not authorized to update this account.');
        }

        $account->update(
            name: $input->name,
            type: $input->type,
            accountNumber: $input->accountNumber,
            providerId: $input->providerId,
            providerName: null, // Mapper will reload it from DB if needed
            isActive: $input->isActive,
        );

        $this->accountRepository->save($account);

        return AccountOutput::fromDomain($account);
    }
}
