<?php

namespace App\Modules\Budget\Application\UseCases;

use App\Modules\Budget\Domain\Repositories\BudgetRepositoryInterface;

final class DeleteBudgetUseCase
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function execute(int $id, int $userId): void
    {
        $budget = $this->budgetRepository->findById($id);

        if (! $budget || $budget->getUserId() !== $userId) {
            abort(404, 'Budget not found.');
        }

        $this->budgetRepository->delete($id);
    }
}

