<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\ProjectApplication\AcceptProjectApplication;
use App\Actions\ProjectApplication\CreateProjectApplication;
use App\Http\Resources\ProjectApplicationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class ProjectApplicationController
{
    public function index(Request $request, int $projectId): ApiResponse
    {
        Gate::forUser($request->user())->authorize('viewAny', ProjectApplication::class);

        $project = Project::findOrFail($projectId);

        /** @var User */
        $client = $request->user();

        // This prevents logged in client from viewing projects they don't own
        // Throwing 404 is more secure, to give an illusion.
        abort_unless(
            $client->id === $project->client_id,
            Response::HTTP_NOT_FOUND,
            __('Project not found')
        );

        $pageSize = $request->integer('pageSize', 10);
        $page = $request->integer('page', 1);

        $applications = ProjectApplication::query()
            ->where('project_id', $project->id)
            ->latest()
            ->simplePaginate(
                perPage: $pageSize,
                page: $page
            );

        return new ApiResponse(
            'Project applications retrieved',
            ProjectApplicationResource::collection($applications),
        );
    }

    public function store(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        app(CreateProjectApplication::class)->handle(
            $request->user(),
            $project,
            $request->input()
        );

        return new ApiResponse('Application sent');
    }

    public function show(Request $request, int $applicationId): ApiResponse
    {
        $application = ProjectApplication::query()
            ->with([
                'freelancer',
                'project',
            ])
            ->findOrFail($applicationId);

        Gate::forUser($request->user())->authorize('view', $application);

        if (! $application->hasBeenViewed()) {
            $application->update([
                'viewed_at' => now(),
            ]);
        }

        return new ApiResponse(
            'Project application retrieved',
            ProjectApplicationResource::make($application),
        );
    }

    public function accept(Request $request, int $applicationId): ApiResponse
    {
        $application = ProjectApplication::query()
            ->with([
                'project',
                'project.owner',
            ])
            ->findOrFail($applicationId);

        app(AcceptProjectApplication::class)->handle(
            $request->user(),
            $application,
        );

        return new ApiResponse('Project application accepted');
    }
}
