<?php

namespace App\Policies;

use App\Domain\Transfer\Models\Transfer;
use App\Models\User;

class TransferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User can only view transfers involving their own accounts.
     */
    public function view(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->fromAccount->user_id
            || $user->id === $transfer->toAccount->user_id;
    }

    /**
     * User can create transfers — account ownership validated in action layer.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User can only update transfers they own (from_account belongs to them).
     */
    public function update(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->fromAccount->user_id;
    }

    /**
     * User can only delete transfers they own.
     */
    public function delete(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->fromAccount->user_id;
    }
}
