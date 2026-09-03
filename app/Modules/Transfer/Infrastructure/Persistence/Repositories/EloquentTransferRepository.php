<?php

namespace App\Modules\Transfer\Infrastructure\Persistence\Repositories;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transfer\Domain\Entities\Transfer;
use App\Modules\Transfer\Domain\Repositories\TransferRepositoryInterface;
use App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Mappers\TransferMapper;
use App\Modules\Transfer\Infrastructure\Persistence\Eloquent\Models\TransferModel;
use Illuminate\Validation\ValidationException;

final class EloquentTransferRepository implements TransferRepositoryInterface
{
    public function save(Transfer $transfer): Transfer
    {
        $data = TransferMapper::toPersistence($transfer);

        if ($transfer->getId() === 0) {
            // Validate same-user ownership of both accounts
            $fromAccount = AccountModel::find($transfer->getFromAccountId());
            $toAccount = AccountModel::find($transfer->getToAccountId());

            if ($fromAccount && $toAccount && $fromAccount->user_id !== $toAccount->user_id) {
                throw ValidationException::withMessages([
                    'to_account_id' => 'Transfer can only be made between accounts belonging to the same user.',
                ]);
            }

            $model = TransferModel::create($data);

            // Create double-entry ledger records
            LedgerEntryModel::create([
                'account_id' => $transfer->getFromAccountId(),
                'direction' => 'debit',
                'amount' => $transfer->getAmount(),
                'currency' => $transfer->getCurrency(),
            ]);

            LedgerEntryModel::create([
                'account_id' => $transfer->getToAccountId(),
                'direction' => 'credit',
                'amount' => $transfer->getAmount(),
                'currency' => $transfer->getCurrency(),
            ]);
        } else {
            /** @var TransferModel $model */
            $model = TransferModel::query()->findOrFail($transfer->getId());
            $model->update($data);
        }

        return TransferMapper::toDomain($model);
    }

    public function findById(int $id): ?Transfer
    {
        $model = TransferModel::query()->find($id);

        return $model ? TransferMapper::toDomain($model) : null;
    }

    /**
     * @return array{data: Transfer[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, int $perPage = 15, int $page = 1): array
    {
        $paginator = TransferModel::query()
            ->whereHas('fromAccount', fn ($q) => $q->where('user_id', $userId))
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = array_map(
            fn (TransferModel $model) => TransferMapper::toDomain($model),
            $paginator->items()
        );

        return [
            'data' => $items,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }
}
