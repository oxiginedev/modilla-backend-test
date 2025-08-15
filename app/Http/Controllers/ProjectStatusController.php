<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Project\UpdateProjectStatus;
use App\Enums\ProjectStatus;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use Illuminate\Http\Request;

final class ProjectStatusController
{
    public function open(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        app(UpdateProjectStatus::class)->handle(
            $request->user(),
            $project,
            ProjectStatus::OPEN,
        );

        return new ApiResponse('Project status updated');
    }

    public function close(Request $request, int $projectId): ApiResponse
    {
        $project = Project::findOrFail($projectId);

        app(UpdateProjectStatus::class)->handle(
            $request->user(),
            $project,
            ProjectStatus::CLOSED,
        );

        return new ApiResponse('Project status updated');
    }
}
