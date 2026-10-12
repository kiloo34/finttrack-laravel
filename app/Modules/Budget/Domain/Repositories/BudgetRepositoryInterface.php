<?php

namespace App\Modules\Budget\Domain\Repositories;

use App\Modules\Budget\Domain\Entities\Budget;

interface BudgetRepositoryInterface
{
    public function save(Budget $budget): Budget;

    public function findById(int $id): ?Budget;

    /**
     * @return Budget[]
     */
    public function findByUserAndPeriod(int $userId, int $month, int $year): array;

    public function findByUserCategoryAndPeriod(int $userId, int $categoryId, int $month, int $year, ?int $excludeId = null): ?Budget;

    public function delete(int $id): void;

    public function getSpentAmount(int $userId, int $categoryId, int $month, int $year): float;

    /**
     * @param  int[]  $categoryIds
     * @return array<int, float> [categoryId => spentAmount]
     */
    public function getSpentAmountsForCategories(int $userId, array $categoryIds, int $month, int $year): array;

    public function getCategoryName(int $categoryId): string;

    /**
     * @param  int[]  $categoryIds
     * @return array<int, string> [categoryId => categoryName]
     */
    public function getCategoryNames(array $categoryIds): array;
}

