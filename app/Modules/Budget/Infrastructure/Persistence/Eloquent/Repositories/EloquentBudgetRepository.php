<?php

namespace App\Modules\Budget\Infrastructure\Persistence\Eloquent\Repositories;

use App\Modules\Budget\Domain\Entities\Budget;
use App\Modules\Budget\Domain\Repositories\BudgetRepositoryInterface;
use App\Modules\Budget\Infrastructure\Persistence\Eloquent\Models\BudgetModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

final class EloquentBudgetRepository implements BudgetRepositoryInterface
{
    public function save(Budget $budget): Budget
    {
        if ($budget->getId() === 0) {
            $model = BudgetModel::create([
                'user_id' => $budget->getUserId(),
                'category_id' => $budget->getCategoryId(),
                'amount' => $budget->getAmount(),
                'month' => $budget->getMonth(),
                'year' => $budget->getYear(),
            ]);
            $budget->assignId($model->id);
        } else {
            /** @var BudgetModel $model */
            $model = BudgetModel::query()->findOrFail($budget->getId());
            $model->update([
                'category_id' => $budget->getCategoryId(),
                'amount' => $budget->getAmount(),
                'month' => $budget->getMonth(),
                'year' => $budget->getYear(),
            ]);
        }

        return $this->toDomain($model);
    }

    public function findById(int $id): ?Budget
    {
        $model = BudgetModel::query()->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    /**
     * @return Budget[]
     */
    public function findByUserAndPeriod(int $userId, int $month, int $year): array
    {
        return BudgetModel::query()
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->map(fn (BudgetModel $model) => $this->toDomain($model))
            ->all();
    }

    public function findByUserCategoryAndPeriod(int $userId, int $categoryId, int $month, int $year, ?int $excludeId = null): ?Budget
    {
        $query = BudgetModel::query()
            ->where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('month', $month)
            ->where('year', $year);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $model = $query->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function delete(int $id): void
    {
        BudgetModel::query()->where('id', $id)->delete();
    }

    public function getSpentAmount(int $userId, int $categoryId, int $month, int $year): float
    {
        $spent = DB::table('transactions')
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.user_id', $userId)
            ->where('transactions.category_id', $categoryId)
            ->where('transactions.type', 'expense')
            ->whereNull('transactions.deleted_at')
            ->whereYear('transactions.transaction_date', $year)
            ->whereMonth('transactions.transaction_date', $month)
            ->sum('transactions.amount');

        return (float) ($spent ?: 0);
    }

    /**
     * @param  int[]  $categoryIds
     * @return array<int, float>
     */
    public function getSpentAmountsForCategories(int $userId, array $categoryIds, int $month, int $year): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        /** @var array<int|string, numeric-string|float|int> $results */
        $results = DB::table('transactions')
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('accounts.user_id', $userId)
            ->whereIn('transactions.category_id', $categoryIds)
            ->where('transactions.type', 'expense')
            ->whereNull('transactions.deleted_at')
            ->whereYear('transactions.transaction_date', $year)
            ->whereMonth('transactions.transaction_date', $month)
            ->groupBy('transactions.category_id')
            ->selectRaw('transactions.category_id, SUM(transactions.amount) as total_spent')
            ->pluck('total_spent', 'category_id')
            ->all();

        $spentMap = [];
        foreach ($results as $catId => $spent) {
            $spentMap[(int) $catId] = (float) $spent;
        }

        return $spentMap;
    }

    public function getCategoryName(int $categoryId): string
    {
        return (string) (CategoryModel::query()->where('id', $categoryId)->value('name') ?? '');
    }

    /**
     * @param  int[]  $categoryIds
     * @return array<int, string>
     */
    public function getCategoryNames(array $categoryIds): array
    {
        if (empty($categoryIds)) {
            return [];
        }

        /** @var array<int|string, string> $results */
        $results = CategoryModel::query()
            ->whereIn('id', $categoryIds)
            ->pluck('name', 'id')
            ->all();

        $names = [];
        foreach ($results as $catId => $name) {
            $names[(int) $catId] = $name;
        }

        return $names;
    }

    private function toDomain(BudgetModel $model): Budget
    {
        return Budget::reconstitute(
            id: (int) $model->id,
            userId: (int) $model->user_id,
            categoryId: (int) $model->category_id,
            amount: (float) $model->amount,
            month: (int) $model->month,
            year: (int) $model->year,
            createdAt: new DateTimeImmutable($model->created_at?->toDateTimeString() ?? 'now'),
            updatedAt: new DateTimeImmutable($model->updated_at?->toDateTimeString() ?? 'now'),
        );
    }
}

