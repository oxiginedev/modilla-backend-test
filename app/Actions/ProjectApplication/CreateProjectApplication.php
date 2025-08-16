<?php

declare(strict_types=1);

namespace App\Actions\ProjectApplication;

use App\Events\ProjectApplicationCreated;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class CreateProjectApplication
{
    public function handle(User $freelancer, Project $project, array $input): void
    {
        Gate::forUser($freelancer)->authorize('create', ProjectApplication::class);

        if (! $project->isAcceptingApplications()) {
            abort(400, 'This project doesn\'t exist or is no longer accepting applications');
        }

        // This was initially part of the validate function
        // I didn't feel a 422 error was the best for this, hence the removal
        if ($freelancer->hasAppliedToProject($project)) {
            abort(400, 'You have previously applied to this project');
        }

        $this->validate($input);

        $application = $project->applications()->create([
            'freelancer_id' => $freelancer->id,
            'bid_amount' => data_get($input, 'bid_amount'),
            'cover_letter_url' => $input['cover_letter'],
        ]);

        ProjectApplicationCreated::dispatch($application);
    }

    private function validate(array $input)
    {
        Validator::make($input, [
            'bid_amount' => ['nullable', 'integer', 'min:0'],
            'cover_letter' => ['required', 'string'],
        ])->validate();
    }
}
