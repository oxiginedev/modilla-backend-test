<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

final class UpdateProject
{
    public function handle(User $user, Project $project, array $input): Project
    {
        Gate::forUser($user)->authorize('update', $project);

        $this->validate($input);

        $project->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'budget_min_amount' => $input['budget_min_amount'],
            'budget_max_amount' => $input['budget_max_amount'],
            'application_closes_at' => data_get($input, 'application_closes_at'),
        ]);

        $project->refresh();

        return $project;
    }

    private function validate(array $input): void
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'budget_min_amount' => ['required', 'integer', 'min:1000'],
            'budget_max_amount' => ['required', 'integer', 'max:9999999'],
            'application_closes_at' => ['nullable', 'date', 'after:today'],
        ])->validate();
    }
}
