<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Repositories;

use App\Modules\Ledger\Infrastructure\Persistence\Eloquent\Models\LedgerEntryModel;
use App\Modules\Transaction\Domain\Entities\Transaction;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Mappers\TransactionMapper;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;

final class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction
    {
        $data = TransactionMapper::toPersistence($transaction);

        if ($transaction->getId() === 0) {
            // New transaction — create and write ledger entry
            $model = TransactionModel::create($data);

            // Create immutable ledger entry
            LedgerEntryModel::create([
                'account_id' => $transaction->getAccountId(),
                'transaction_id' => $model->id,
                'direction' => $transaction->ledgerDirection(),
                'amount' => abs((float) $transaction->getAmount()),
                'currency' => $transaction->getCurrency(),
            ]);
        } else {
            /** @var TransactionModel $model */
            $model = TransactionModel::query()->findOrFail($transaction->getId());
            $changed = $model->fill($data)->isDirty(['amount', 'type', 'account_id']);
            $model->save();

            // Update related ledger entries if financial fields changed
            if ($changed) {
                $model->ledgerEntries()->update([
                    'account_id' => $transaction->getAccountId(),
                    'direction' => $transaction->ledgerDirection(),
                    'amount' => abs((float) $transaction->getAmount()),
                ]);
            }
        }

        return TransactionMapper::toDomain($model);
    }

    public function findById(int $id): ?Transaction
    {
        $model = TransactionModel::query()->find($id);

        return $model ? TransactionMapper::toDomain($model) : null;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: Transaction[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, array $filters, int $perPage = 15, int $page = 1): array
    {
        $query = TransactionModel::query()
            ->whereHas('account', fn ($q) => $q->where('user_id', $userId));

        if (! empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
            $query->whereBetween('transaction_date', [$filters['start_date'], $filters['end_date']]);
        }

        $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(fn ($m) => TransactionMapper::toDomain($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    public function delete(int $id): void
    {
        TransactionModel::query()->where('id', $id)->delete();
    }
}
