<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\ProjectCreated;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProjectTest extends TestCase
{
    public function test_clients_can_create_projects(): void
    {
        Event::fake(ProjectCreated::class);
        $user = User::factory()->client()->create();

        Sanctum::actingAs($user);

        $response = $this->post('/projects', [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'budget_min_amount' => 1000,
            'budget_max_amount' => 5000,
        ]);

        Event::assertDispatched(ProjectCreated::class);

        $this->assertDatabaseCount('projects', 1);

        $response->assertCreated();
    }

    public function test_freelancers_cant_create_projects(): void
    {
        $user = User::factory()->freelancer()->create();

        Sanctum::actingAs($user);

        $response = $this->post('/projects', [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'budget_min_amount' => 1000,
            'budget_max_amount' => 5000,
        ]);

        $response->assertForbidden();
    }

    public function test_fetches_all_projects_without_filters(): void
    {
        Project::factory()->count(3)->open()->create();
        Project::factory()->count(3)->closed()->create();
        Project::factory()->count(3)->draft()->create();

        $response = $this->getJson('/projects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'budget_min_amount',
                        'budget_max_amount',
                        'is_accepting_applications',
                        'created_at',
                        'updated_at',
                        'status',
                        'days_since_created',
                    ],
                ],
                'links',
                'meta',
            ])->assertJsonCount(6, 'data');
    }

    public function test_filters_project_list_by_status(): void
    {
        Project::factory()->count(3)->draft()->create();
        Project::factory()->count(7)->open()->create();
        Project::factory()->count(5)->closed()->create();

        $this->getJson('/projects?status=draft')->assertUnprocessable();

        $this->getJson('/projects?status=open')
            ->assertOk()
            ->assertJsonCount(7, 'data');

        $this->getJson('/projects?status=closed')
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function test_filters_project_list_by_search_query(): void
    {
        Project::factory()->open()->create(['title' => 'Golang Developer']);
        Project::factory()->open()->create(['title' => 'Junior react Engineer']);
        Project::factory()->closed()->create(['title' => 'Senior React Developer']);

        $response = $this->getJson('/projects?q=react')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $project) {
            $this->assertStringContainsStringIgnoringCase('react', $project['title']);
        }
    }

    public function test_filters_project_list_by_budget_minimum(): void
    {
        Project::factory()->open()->create([
            'budget_min_amount' => 1000,
            'budget_max_amount' => 2000,
        ]);
        Project::factory()->open()->create([
            'budget_min_amount' => 3000,
            'budget_max_amount' => 4000,
        ]);
        Project::factory()->open()->create([
            'budget_min_amount' => 5000,
            'budget_max_amount' => 6000,
        ]);

        $response = $this->getJson('/projects?budgetMin=4000')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        foreach ($response->json('data') as $project) {
            $this->assertGreaterThanOrEqual(4000, $project['budget_min_amount']);
        }
    }

    public function test_filters_project_list_by_budget_maximum(): void
    {
        Project::factory()->open()->create([
            'budget_min_amount' => 1000,
            'budget_max_amount' => 2000,
        ]);
        Project::factory()->open()->create([
            'budget_min_amount' => 3000,
            'budget_max_amount' => 4000,
        ]);
        Project::factory()->open()->create([
            'budget_min_amount' => 5000,
            'budget_max_amount' => 6000,
        ]);

        $response = $this->getJson('/projects?budgetMax=4500')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        foreach ($response->json('data') as $project) {
            $this->assertLessThanOrEqual(4500, $project['budget_max_amount']);
        }
    }
}
