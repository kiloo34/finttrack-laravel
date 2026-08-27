<?php

namespace App\Http\Controllers\Api;

use App\Domain\Audit\Services\AuditService;
use App\Domain\Category\Models\Category;
use App\Domain\User\Models\User;
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
        $category->user_id = auth()->id();
        $category->is_system = false;
        $category->save();

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'category_created',
            'Created category: '.$category->name,
            ['category_id' => $category->id]
        );

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

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'category_updated',
            'Updated category: '.$category->name,
            ['category_id' => $category->id]
        );

        return new CategoryResource($category);
    }

    public function destroy(Category $category): Response
    {
        $this->authorize('delete', $category);

        $category->delete();

        /** @var User $user */
        $user = auth()->user();

        $this->auditService->log(
            $user,
            'category_deleted',
            'Deleted category: '.$category->name,
            ['category_id' => $category->id]
        );

        return response()->noContent();
    }
}
