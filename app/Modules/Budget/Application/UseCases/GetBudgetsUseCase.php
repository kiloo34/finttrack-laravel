<?php

namespace App\Modules\Budget\Application\UseCases;

use App\Modules\Budget\Application\DTOs\BudgetOutput;
use App\Modules\Budget\Domain\Entities\Budget;
use App\Modules\Budget\Domain\Repositories\BudgetRepositoryInterface;

final class GetBudgetsUseCase
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    /**
     * @return BudgetOutput[]
     */
    public function execute(int $userId, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? (int) now()->format('n');
        $year = $year ?? (int) now()->format('Y');

        $budgets = $this->budgetRepository->findByUserAndPeriod($userId, $month, $year);

        if (empty($budgets)) {
            return [];
        }

        $categoryIds = array_map(fn (Budget $b) => $b->getCategoryId(), $budgets);
        $categoryNames = $this->budgetRepository->getCategoryNames($categoryIds);
        $spentAmounts = $this->budgetRepository->getSpentAmountsForCategories($userId, $categoryIds, $month, $year);

        $outputs = [];
        foreach ($budgets as $budget) {
            $outputs[] = $this->buildOutput(
                budget: $budget,
                categoryName: $categoryNames[$budget->getCategoryId()] ?? '',
                spentAmount: $spentAmounts[$budget->getCategoryId()] ?? 0.0,
            );
        }

        return $outputs;
    }

    public function getById(int $id, int $userId): ?BudgetOutput
    {
        $budget = $this->budgetRepository->findById($id);

        if (! $budget || $budget->getUserId() !== $userId) {
            return null;
        }

        $categoryName = $this->budgetRepository->getCategoryName($budget->getCategoryId());
        $spentAmount = $this->budgetRepository->getSpentAmount(
            userId: $userId,
            categoryId: $budget->getCategoryId(),
            month: $budget->getMonth(),
            year: $budget->getYear(),
        );

        return $this->buildOutput($budget, $categoryName, $spentAmount);
    }

    public function buildOutput(Budget $budget, string $categoryName, float $spentAmount): BudgetOutput
    {
        $amount = $budget->getAmount();
        $remaining = max(0.0, round($amount - $spentAmount, 2));
        $percentage = $amount > 0 ? round(($spentAmount / $amount) * 100, 1) : 0.0;

        return new BudgetOutput(
            id: $budget->getId(),
            categoryId: $budget->getCategoryId(),
            categoryName: $categoryName,
            amount: $amount,
            spentAmount: $spentAmount,
            remainingAmount: $remaining,
            percentage: $percentage,
            month: $budget->getMonth(),
            year: $budget->getYear(),
            createdAt: $budget->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $budget->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

