<?php

declare(strict_types=1);

namespace App\Actions\ProjectApplication;

use App\Enums\ProjectStatus;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class AcceptProjectApplication
{
    public function handle(User $client, ProjectApplication $application): void
    {
        Gate::forUser($client)->authorize('update', $application);

        DB::transaction(function () use ($application) {
            $application->update([
                'accepted_at' => now(),
            ]);

            $application->project->update([
                'status' => ProjectStatus::CLOSED,
            ]);
        });
    }
}
