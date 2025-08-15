<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Enums\ProjectStatus;
use App\Events\ProjectCreated;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class CreateProject
{
    public function handle(User $user, array $input)
    {
        Gate::forUser($user)->authorize('create', Project::class);

        $this->validate($input);

        $project = Project::create([
            'client_id' => $user->id,
            'title' => $input['title'],
            'description' => $input['description'],
            'budget_min_amount' => $input['budget_min_amount'],
            'budget_max_amount' => $input['budget_max_amount'],
            'application_closes_at' => data_get($input, 'application_closes_at'),
            'status' => $input['publish_now']
                ? ProjectStatus::OPEN
                : ProjectStatus::DRAFT,
        ]);

        ProjectCreated::dispatch($project);

        return $project;
    }

    private function validate(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'budget_min_amount' => ['required', 'integer', 'min:1000'],
            'budget_max_amount' => ['required', 'integer', 'max:9999999'],
            'application_closes_at' => ['nullable', 'date', 'after:today'],
            'publish_now' => ['required', 'boolean'],
        ])->validate();
    }
}
