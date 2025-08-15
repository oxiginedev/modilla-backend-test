<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class DeleteProject
{
    public function handle(User $user, Project $project)
    {
        Gate::forUser($user)->authorize('delete', $project);

        $project->delete();
    }
}
