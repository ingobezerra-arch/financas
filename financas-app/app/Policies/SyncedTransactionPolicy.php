<?php

namespace App\Policies;

use App\Models\SyncedTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SyncedTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SyncedTransaction $syncedTransaction): bool
    {
        return (int) $user->id === (int) $syncedTransaction->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SyncedTransaction $syncedTransaction): bool
    {
        return (int) $user->id === (int) $syncedTransaction->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SyncedTransaction $syncedTransaction): bool
    {
        return (int) $user->id === (int) $syncedTransaction->user_id;
    }
}