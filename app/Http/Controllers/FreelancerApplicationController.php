<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ProjectApplicationResource;
use App\Http\Responses\ApiResponse;
use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class FreelancerApplicationController
{
    public function __invoke(Request $request): ApiResponse
    {
        $pageSize = $request->integer('pageSize', 10);
        $page = $request->integer('page', 1);

        $applications = ProjectApplication::query()
            ->where('freelancer_id', Auth::id())
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
}
