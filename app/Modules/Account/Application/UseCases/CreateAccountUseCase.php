<?php

namespace App\Modules\Account\Application\UseCases;

use App\Modules\Account\Application\DTOs\AccountOutput;
use App\Modules\Account\Application\DTOs\CreateAccountInput;
use App\Modules\Account\Domain\Entities\Account;
use App\Modules\Account\Domain\Repositories\AccountRepositoryInterface;

final class CreateAccountUseCase
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(CreateAccountInput $input): AccountOutput
    {
        $account = Account::create(
            userId: $input->userId,
            name: $input->name,
            type: $input->type,
            currency: $input->currency,
            accountNumber: $input->accountNumber,
            providerId: $input->providerId,
            initialBalance: $input->initialBalance,
            isActive: $input->isActive,
        );

        $this->accountRepository->save($account);

        return AccountOutput::fromDomain($account);
    }
}
