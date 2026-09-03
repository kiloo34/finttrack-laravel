<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Application\DTOs\AccountOutput;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;

final class GetAccountsUseCase
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    /** @return AccountOutput[] */
    public function execute(int $userId): array
    {
        $accounts = $this->accountRepository->findByUserId($userId);

        return array_map(
            fn ($account) => AccountOutput::fromDomain($account),
            $accounts,
        );
    }
}
