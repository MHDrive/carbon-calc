<?php

namespace App\Policies;

use App\Models\ClimateWet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClimateWetPolicy
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
    public function view(User $user, ClimateWet $climateWet): bool
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
    public function update(User $user, ClimateWet $climateWet): bool
    {
        return $user->isGuest() && $user->access_calc;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClimateWet $climateWet): bool
    {
        return $user->isGuest() && $user->access_calc;
    }
}
