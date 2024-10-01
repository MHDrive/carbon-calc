<?php

namespace App\Policies;

use App\Models\ClimateDry;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClimateDryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isGuest() && $user->access_calc;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClimateDry $climateDry): bool
    {
        return $user->isGuest() && $user->access_calc;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isGuest() && $user->access_calc;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClimateDry $climateDry): bool
    {
        return $user->isGuest() && $user->access_calc;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClimateDry $climateDry): bool
    {
        return $user->isGuest() && $user->access_calc;
    }
}
