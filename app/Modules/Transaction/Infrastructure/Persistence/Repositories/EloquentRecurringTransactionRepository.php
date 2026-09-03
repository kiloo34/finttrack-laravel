<?php

namespace App\Modules\Transaction\Infrastructure\Persistence\Repositories;

use App\Modules\Transaction\Domain\Entities\RecurringTransaction;
use App\Modules\Transaction\Domain\Repositories\RecurringTransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Mappers\RecurringTransactionMapper;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\RecurringTransactionModel;
use Carbon\Carbon;
use DateTimeInterface;

final class EloquentRecurringTransactionRepository implements RecurringTransactionRepositoryInterface
{
    public function save(RecurringTransaction $transaction): RecurringTransaction
    {
        $data = RecurringTransactionMapper::toPersistence($transaction);

        if ($transaction->getId() === 0) {
            $model = RecurringTransactionModel::create($data);
        } else {
            /** @var RecurringTransactionModel $model */
            $model = RecurringTransactionModel::query()->findOrFail($transaction->getId());
            $model->update($data);
        }

        return RecurringTransactionMapper::toDomain($model);
    }

    public function findById(int $id): ?RecurringTransaction
    {
        $model = RecurringTransactionModel::query()->find($id);

        return $model ? RecurringTransactionMapper::toDomain($model) : null;
    }

    /**
     * @return RecurringTransaction[]
     */
    public function getActiveDueTransactions(DateTimeInterface $date): array
    {
        $models = RecurringTransactionModel::query()
            ->where('is_active', true)
            ->where('next_run_date', '<=', Carbon::instance($date)->endOfDay()->toDateTimeString())
            ->get();

        return $models->map(fn ($m) => RecurringTransactionMapper::toDomain($m))->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{data: RecurringTransaction[], total: int, per_page: int, current_page: int}
     */
    public function paginateForUser(int $userId, array $filters, int $perPage = 15, int $page = 1): array
    {
        $query = RecurringTransactionModel::query()
            ->where('user_id', $userId);

        if (! empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('next_run_date', 'asc')->orderBy('id', 'desc');

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => array_map(fn ($m) => RecurringTransactionMapper::toDomain($m), $paginator->items()),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }

    public function delete(int $id): void
    {
        RecurringTransactionModel::query()->where('id', $id)->delete();
    }
}
