<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ProjectApplication;
use App\Models\User;

final class ProjectApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::CLIENT);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::FREELANCER);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProjectApplication $projectApplication): bool
    {
        return $user->is($projectApplication->project->owner);
    }
}
