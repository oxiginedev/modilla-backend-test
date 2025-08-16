<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedUserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FreelancerApplicationController;
use App\Http\Controllers\ProjectApplicationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => new ApiResponse('Mondilla Backend Test'));

Route::post('/auth/register', [RegisterController::class, 'store']);
Route::post('/auth/login', [AuthenticatedUserController::class, 'store']);

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{projectId}', [ProjectController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{projectId}', [ProjectController::class, 'update']);
    Route::delete('/projects/{projectId}', [ProjectController::class, 'destroy']);

    Route::post('/projects/{projectId}/open', [ProjectStatusController::class, 'open']);
    Route::post('/projects/{projectId}/close', [ProjectStatusController::class, 'close']);

    Route::get('/projects/{projectId}/applications', [ProjectApplicationController::class, 'index']);
    Route::post('/projects/{projectId}/applications', [ProjectApplicationController::class, 'store']);

    Route::post('/applications/{applicationId}/accept', [ProjectApplicationController::class, 'accept']);

    Route::get('/me/applications', FreelancerApplicationController::class);

    Route::post('/auth/logout', [AuthenticatedUserController::class, 'destroy']);
});
