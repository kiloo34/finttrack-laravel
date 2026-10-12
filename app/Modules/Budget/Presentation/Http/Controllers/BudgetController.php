<?php

namespace App\Modules\Budget\Presentation\Http\Controllers;

use App\Modules\Budget\Application\UseCases\CreateBudgetUseCase;
use App\Modules\Budget\Application\UseCases\DeleteBudgetUseCase;
use App\Modules\Budget\Application\UseCases\GetBudgetsUseCase;
use App\Modules\Budget\Application\UseCases\UpdateBudgetUseCase;
use App\Modules\Budget\Presentation\Http\Requests\StoreBudgetHttpRequest;
use App\Modules\Budget\Presentation\Http\Requests\UpdateBudgetHttpRequest;
use App\Modules\Budget\Presentation\Http\Resources\BudgetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class BudgetController
{
    public function __construct(
        private readonly GetBudgetsUseCase $getBudgetsUseCase,
        private readonly CreateBudgetUseCase $createBudgetUseCase,
        private readonly UpdateBudgetUseCase $updateBudgetUseCase,
        private readonly DeleteBudgetUseCase $deleteBudgetUseCase,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        $month = $request->query('month') !== null ? (int) $request->query('month') : null;
        $year = $request->query('year') !== null ? (int) $request->query('year') : null;

        $outputs = $this->getBudgetsUseCase->execute($userId, $month, $year);

        return BudgetResource::collection(collect($outputs));
    }

    public function store(StoreBudgetHttpRequest $request): JsonResponse
    {
        $output = $this->createBudgetUseCase->execute($request->toDto());

        return (new BudgetResource($output))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        $output = $this->getBudgetsUseCase->getById($id, $userId);

        if (! $output) {
            abort(404, 'Budget not found.');
        }

        return (new BudgetResource($output))->response();
    }

    public function update(UpdateBudgetHttpRequest $request, int $id): JsonResponse
    {
        $output = $this->updateBudgetUseCase->execute($request->toDto($id));

        return (new BudgetResource($output))->response();
    }

    public function destroy(int $id): Response
    {
        /** @var int $userId */
        $userId = (int) auth()->id();

        $this->deleteBudgetUseCase->execute($id, $userId);

        return response()->noContent();
    }
}

