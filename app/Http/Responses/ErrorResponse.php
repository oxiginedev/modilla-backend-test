<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

final class ErrorResponse implements Responsable
{
    public function __construct(
        private string $message,
        private ?array $errors = null,
        private int $status = 400,
    ) {}

    public function toResponse($request): JsonResponse
    {
        $response = [];
        $response['message'] = $this->message;

        if ($this->errors !== null) {
            $response['errors'] = $this->errors;
        }

        return new JsonResponse($response, $this->status);
    }
}
