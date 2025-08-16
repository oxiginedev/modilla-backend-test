<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Project;

use App\Actions\Project\CreateProject;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

final class CreateProjectTest extends TestCase
{
    public function test_client_can_create_project(): void
    {
        $client = User::factory()->client()->create();

        $action = new CreateProject;

        $project = $action->handle($client, [
            'title' => 'Test Project',
            'description' => 'Test Description',
            'budget_min_amount' => 1000,
            'budget_max_amount' => 3000,
        ]);

        $this->assertInstanceOf(Project::class, $project);
    }
}
