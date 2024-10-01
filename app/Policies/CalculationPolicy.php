<?php

namespace App\Policies;

use App\Models\User;
use App\Models\calculation;
use App\Models\Submission;
use Illuminate\Auth\Access\Response;

class CalculationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGuest();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, calculation $calculation): bool
    {
        return $user->isGuest();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isGuest();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, calculation $calculation): bool
    {
        return $user->isGuest();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, calculation $calculation): bool
    {
        return $user->isGuest();
    }
}
