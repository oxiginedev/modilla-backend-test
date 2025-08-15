<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class UpdateProjectStatus
{
    public function handle(User $user, Project $project, ProjectStatus $status)
    {
        Gate::forUser($user)->authorize('update', $project);

        $project->update([
            'status' => $status,
        ]);
    }
}
