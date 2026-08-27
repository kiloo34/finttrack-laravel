<?php

namespace App\Http\Controllers\Api;

use App\Domain\Audit\Services\AuditService;
use App\Domain\Category\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->where('user_id', auth()->id())
            ->orWhere('is_system', true)
            ->get();

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = new Category($request->validated());
        /** @var int<1, max> $userId */
        $userId = auth()->id();
        $category->user_id = $userId;
        $category->is_system = false;
        $category->save();

        $this->auditService->log('category_created', $category);

        return new CategoryResource($category);
    }

    public function show(Category $category): CategoryResource
    {
        $this->authorize('view', $category);

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $this->authorize('update', $category);

        $category->update($request->validated());

        $this->auditService->log('category_updated', $category, $category->getOriginal() ?: [], $category->getAttributes());

        return new CategoryResource($category);
    }

    public function destroy(Category $category): Response
    {
        $this->authorize('delete', $category);

        $category->delete();

        $this->auditService->log('category_deleted', $category, $category->getAttributes());

        return response()->noContent();
    }
}
