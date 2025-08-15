<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

final class ApiResponse implements Responsable
{
    public function __construct(
        private string $message,
        private null|array|JsonResource|AnonymousResourceCollection $data = null,
        private int $status = 200,
    ) {}

    public function toResponse($request): JsonResponse
    {
        $response = [];
        $response['message'] = $this->message;

        if ($this->data !== null) {
            $response['data'] = $this->data;
        }

        return new JsonResponse($response, $this->status);
    }
}
