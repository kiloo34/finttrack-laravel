<?php

namespace App\Policies;

use App\Domain\Category\Models\Category;
use App\Domain\User\Models\User;

class CategoryPolicy
{
    /**
     * User can view any — includes system categories.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User can view their own categories or system categories.
     */
    public function view(User $user, Category $category): bool
    {
        return $category->is_system || $user->id === $category->user_id;
    }

    /**
     * User can create their own categories.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User can only update their own (non-system) categories.
     */
    public function update(User $user, Category $category): bool
    {
        return ! $category->is_system && $user->id === $category->user_id;
    }

    /**
     * User can only delete their own (non-system) categories.
     */
    public function delete(User $user, Category $category): bool
    {
        return ! $category->is_system && $user->id === $category->user_id;
    }
}
