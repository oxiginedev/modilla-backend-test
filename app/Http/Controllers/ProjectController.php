<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Project\CreateProject;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\UpdateProject;
use App\Enums\ProjectStatus;
use App\Http\Resources\ProjectResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class ProjectController
{
    public function index(Request $request)
    {
        $pageSize = $request->integer('pageSize', 10);
        $page = $request->integer('page', 1);
        $status = $request->string('status');
        $budgetMinAmount = $request->integer('budgetMin');
        $budgetMaxAmount = $request->integer('budgetMax');
        $q = $request->string('q');

        $projects = Project::query()
            ->with('owner:id,name')
            // Exclude projects that are still in draft
            // Only return drafts if the owners are authenticated
            ->whereIn('status', [ProjectStatus::OPEN, ProjectStatus::CLOSED])
            ->when($q, function (Builder $query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->when($status, function (Builder $query) use ($status) {
                $query->where('status', $status);
            })
            ->when($budgetMinAmount, function (Builder $query) use ($budgetMinAmount) {
                $query->where('budget_min_amount', '>=', $budgetMinAmount);
            })
            ->when($budgetMaxAmount, function (Builder $query) use ($budgetMaxAmount) {
                $query->where('budget_max_amount', '<=', $budgetMaxAmount);
            })
            ->latest()
            ->simplePaginate(
                perPage: $pageSize,
                page: $page
            );

        return ProjectResource::collection($projects);
    }

    public function store(Request $request): ApiResponse
    {
        $project = app(CreateProject::class)->handle(
            $request->user(),
            $request->input(),
        );

        return new ApiResponse(
            'Project created',
            new ProjectResource($project),
            201,
        );
    }

    public function show(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        return new ApiResponse(
            'Project retrieved',
            ProjectResource::make($project)
        );
    }

    public function update(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        $project = app(UpdateProject::class)->handle(
            $request->user(),
            $project,
            $request->input(),
        );

        return new ApiResponse(
            'Project updated',
            ProjectResource::make($project)
        );
    }

    public function destroy(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        app(DeleteProject::class)->handle(
            $request->user(),
            $project,
        );

        return new ApiResponse('Project deleted');
    }
}
