<?php

namespace App\Modules\Category\Presentation\Http\Controllers;

use App\Modules\Audit\Application\Services\AuditService;
use App\Modules\Category\Application\UseCases\CreateCategoryUseCase;
use App\Modules\Category\Application\UseCases\DeleteCategoryUseCase;
use App\Modules\Category\Application\UseCases\GetCategoriesUseCase;
use App\Modules\Category\Application\UseCases\UpdateCategoryUseCase;
use App\Modules\Category\Presentation\Http\Requests\StoreCategoryHttpRequest;
use App\Modules\Category\Presentation\Http\Requests\UpdateCategoryHttpRequest;
use App\Modules\Category\Presentation\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class CategoryController
{
    public function __construct(
        private readonly CreateCategoryUseCase $createUseCase,
        private readonly UpdateCategoryUseCase $updateUseCase,
        private readonly DeleteCategoryUseCase $deleteUseCase,
        private readonly GetCategoriesUseCase $getCategoriesUseCase,
        private readonly AuditService $auditService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        /** @var int $userId */
        $userId = auth()->id();
        $outputs = $this->getCategoriesUseCase->execute($userId);

        return CategoryResource::collection(collect($outputs));
    }

    public function store(StoreCategoryHttpRequest $request): JsonResponse
    {
        $output = $this->createUseCase->execute($request->toDto());

        $this->auditService->log('category_created', 'category', $output->id);

        return (new CategoryResource($output))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();
        $outputs = $this->getCategoriesUseCase->execute($userId);

        $output = collect($outputs)->firstWhere('id', $id);

        if (! $output) {
            abort(404, 'Category not found.');
        }

        return (new CategoryResource($output))->response();
    }

    public function update(UpdateCategoryHttpRequest $request, int $id): JsonResponse
    {
        /** @var int $userId */
        $userId = auth()->id();

        $output = $this->updateUseCase->execute(
            input: $request->toDto($id),
            actingUserId: $userId,
        );

        $this->auditService->log('category_updated', 'category', $output->id);

        return (new CategoryResource($output))->response();
    }

    public function destroy(int $id): Response
    {
        /** @var int $userId */
        $userId = auth()->id();

        $this->deleteUseCase->execute(id: $id, actingUserId: $userId);

        $this->auditService->log('category_deleted', 'category', $id);

        return response()->noContent();
    }
}
