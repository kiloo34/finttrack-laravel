<?php

namespace App\Modules\Transfer\Application\UseCases;

use App\Modules\Transfer\Application\DTOs\CreateTransferInput;
use App\Modules\Transfer\Application\DTOs\TransferOutput;
use App\Modules\Transfer\Domain\Entities\Transfer;
use App\Modules\Transfer\Domain\Repositories\TransferRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class CreateTransferUseCase
{
    public function __construct(
        private readonly TransferRepositoryInterface $transferRepository,
    ) {}

    public function execute(CreateTransferInput $input): TransferOutput
    {
        return DB::transaction(function () use ($input) {
            $transfer = Transfer::create(
                fromAccountId: $input->fromAccountId,
                toAccountId: $input->toAccountId,
                amount: $input->amount,
                currency: $input->currency,
                transactionDate: $input->transactionDate,
                description: $input->description,
            );

            $saved = $this->transferRepository->save($transfer);

            return TransferOutput::fromDomain($saved);
        });
    }
}
