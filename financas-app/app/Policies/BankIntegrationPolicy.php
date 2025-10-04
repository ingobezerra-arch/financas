<?php

namespace App\Policies;

use App\Models\BankIntegration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankIntegrationPolicy
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
    public function view(User $user, BankIntegration $bankIntegration): bool
    {
        return (int) $user->id === (int) $bankIntegration->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BankIntegration $bankIntegration): bool
    {
        return (int) $user->id === (int) $bankIntegration->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BankIntegration $bankIntegration): bool
    {
        return (int) $user->id === (int) $bankIntegration->user_id;
    }
}