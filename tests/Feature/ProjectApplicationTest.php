<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProjectApplicationTest extends TestCase
{
    public function test_freelancers_can_apply_to_open_projects(): void
    {
        $user = User::factory()->freelancer()->create();
        $project = Project::factory()->open()->create();

        Sanctum::actingAs($user);

        $response = $this->applyToProject($project);

        $this->assertDatabaseHas(ProjectApplication::class, [
            'project_id' => $project->id,
            'freelancer_id' => $user->id,
        ]);

        $response->assertOk();
    }

    public function test_freelancers_cant_create_duplicate_applications(): void
    {
        $user = User::factory()->freelancer()->create();
        $project = Project::factory()->open()->create();

        Sanctum::actingAs($user);

        $response1 = $this->applyToProject($project);

        $this->assertDatabaseHas(ProjectApplication::class, [
            'project_id' => $project->id,
            'freelancer_id' => $user->id,
        ]);

        $response1->assertOk();

        $response2 = $this->applyToProject($project);

        $this->assertDatabaseCount(ProjectApplication::class, 1);

        $response2->assertBadRequest();
    }

    public function test_freelancers_cant_apply_to_project_if_closed(): void
    {
        $user = User::factory()->freelancer()->create();
        $project = Project::factory()->closed()->create();

        Sanctum::actingAs($user);

        $response = $this->applyToProject($project);

        $this->assertDatabaseMissing(ProjectApplication::class, [
            'project_id' => $project->id,
            'freelancer_id' => $user->id,
        ]);

        $response->assertBadRequest();
    }

    private function applyToProject(Project $project): TestResponse
    {
        return $this->post(
            sprintf('/projects/%d/applications', $project->id), [
                'bid_amount' => 3000,
                'cover_letter' => 'Test cover letter',
            ],
        );
    }
}
