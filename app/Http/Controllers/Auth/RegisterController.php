<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUser;
use App\Enums\UserRole;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class RegisterController
{
    public function store(Request $request): ApiResponse
    {
        $input = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:filter', 'unique:users,email'],
            'password' => ['required', 'string', Password::default()],
            'role' => [
                'required',
                'string',
                Rule::enum(UserRole::class)->except(UserRole::ADMIN),
            ],
        ]);

        $user = app(CreateUser::class)->handle($input);

        event(new Registered($user));

        $accessToken = $user->createToken($request->ip())->plainTextToken;

        return new ApiResponse(
            'Registration successful', [
                'access_token' => $accessToken,
                'user' => new UserResource($user),
            ],
            201
        );
    }
}
