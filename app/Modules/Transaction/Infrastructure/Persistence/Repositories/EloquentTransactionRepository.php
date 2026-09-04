<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Repositories;

use App\Modules\Transaction\Domain\Entities\Transaction;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Mappers\TransactionMapper;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use Illuminate\Support\Facades\DB;

final class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function save(Transaction $transaction): Transaction
    {
        return DB::transaction(function () use ($transaction) {
            $data = TransactionMapper::toPersistence($transaction);

            if ($transaction->getId() === 0) {
                // New transaction
                $model = TransactionModel::create($data);
                $transaction->assignId($model->id);
                $transaction->recordCreatedEvent();
            } else {
                // Update existing
                /** @var TransactionModel $model */
                $model = TransactionModel::query()->lockForUpdate()->findOrFail($transaction->getId());
                $model->fill($data);
                $model->save();
            }

            // Dispatch domain events
            foreach ($transaction->flushEvents() as $event) {
                event($event);
            }

            return TransactionMapper::toDomain($model);
        });
    }

    public function findById(int $id): ?Transaction
    {
        $model = TransactionModel::query()->find($id);

        return $model ? TransactionMapper::toDomain($model) : null;
    }

    public function existsSimilarTransaction(int $accountId, string $type, string $amount, \DateTimeInterface $date): bool
    {
        return TransactionModel::query()
            ->where('account_id', $accountId)
            ->where('type', $type)
            ->where('amount', $amount)
            ->whereDate('transaction_date', $date)
            ->exists();
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
        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                    ->orWhere('reference', 'like', $searchTerm);
            });
        }
        if (isset($filters['min_amount']) && $filters['min_amount'] !== '') {
            $query->where('amount', '>=', $filters['min_amount']);
        }
        if (isset($filters['max_amount']) && $filters['max_amount'] !== '') {
            $query->where('amount', '<=', $filters['max_amount']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
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
        DB::transaction(function () use ($id) {
            $model = TransactionModel::query()->lockForUpdate()->find($id);
            if (! $model) {
                return;
            }

            $transaction = TransactionMapper::toDomain($model);
            $transaction->markAsDeleted();

            $model->delete();

            foreach ($transaction->flushEvents() as $event) {
                event($event);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{income: string, expense: string}
     */
    public function getSummaryForUser(int $userId, array $filters): array
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
        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                    ->orWhere('reference', 'like', $searchTerm);
            });
        }
        if (isset($filters['min_amount']) && $filters['min_amount'] !== '') {
            $query->where('amount', '>=', $filters['min_amount']);
        }
        if (isset($filters['max_amount']) && $filters['max_amount'] !== '') {
            $query->where('amount', '<=', $filters['max_amount']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['exclude_reference_prefix'])) {
            $prefix = $filters['exclude_reference_prefix'] . '%';
            $query->where(function ($q) use ($prefix) {
                $q->whereNull('reference')
                  ->orWhere('reference', 'not like', $prefix);
            });
        }

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return [
            'income' => (string) ($income ?: '0.00'),
            'expense' => (string) ($expense ?: '0.00'),
        ];
    }
}
