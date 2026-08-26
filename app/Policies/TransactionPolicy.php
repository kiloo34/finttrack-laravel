<?php

namespace App\Policies;

use App\Domain\Transaction\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User can only view transactions on their own accounts.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    /**
     * User can create transactions — account ownership is validated in the action layer.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User can only update transactions on their own accounts.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    /**
     * User can only delete (soft-delete) transactions on their own accounts.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }
}
