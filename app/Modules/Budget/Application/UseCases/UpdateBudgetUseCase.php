<?php

namespace App\Modules\Budget\Application\UseCases;

use App\Modules\Budget\Application\DTOs\BudgetOutput;
use App\Modules\Budget\Application\DTOs\UpdateBudgetInput;
use App\Modules\Budget\Domain\Repositories\BudgetRepositoryInterface;
use Illuminate\Validation\ValidationException;

final class UpdateBudgetUseCase
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function execute(UpdateBudgetInput $input): BudgetOutput
    {
        $budget = $this->budgetRepository->findById($input->id);

        if (! $budget || $budget->getUserId() !== $input->userId) {
            abort(404, 'Budget not found.');
        }

        $targetCategoryId = $input->categoryId ?? $budget->getCategoryId();
        $targetMonth = $input->month ?? $budget->getMonth();
        $targetYear = $input->year ?? $budget->getYear();

        if (
            $targetCategoryId !== $budget->getCategoryId() ||
            $targetMonth !== $budget->getMonth() ||
            $targetYear !== $budget->getYear()
        ) {
            $existing = $this->budgetRepository->findByUserCategoryAndPeriod(
                userId: $input->userId,
                categoryId: $targetCategoryId,
                month: $targetMonth,
                year: $targetYear,
                excludeId: $budget->getId(),
            );

            if ($existing !== null) {
                throw ValidationException::withMessages([
                    'category_id' => 'Budget for this category and period already exists.',
                ]);
            }
        }

        $budget->update(
            categoryId: $input->categoryId,
            amount: $input->amount,
            month: $input->month,
            year: $input->year,
        );

        $saved = $this->budgetRepository->save($budget);

        $categoryName = $this->budgetRepository->getCategoryName($saved->getCategoryId());
        $spentAmount = $this->budgetRepository->getSpentAmount(
            userId: $saved->getUserId(),
            categoryId: $saved->getCategoryId(),
            month: $saved->getMonth(),
            year: $saved->getYear(),
        );

        $amount = $saved->getAmount();
        $remaining = max(0.0, round($amount - $spentAmount, 2));
        $percentage = $amount > 0 ? round(($spentAmount / $amount) * 100, 1) : 0.0;

        return new BudgetOutput(
            id: $saved->getId(),
            categoryId: $saved->getCategoryId(),
            categoryName: $categoryName,
            amount: $amount,
            spentAmount: $spentAmount,
            remainingAmount: $remaining,
            percentage: $percentage,
            month: $saved->getMonth(),
            year: $saved->getYear(),
            createdAt: $saved->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $saved->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }
}

