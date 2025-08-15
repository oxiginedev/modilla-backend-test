<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

final class AuthenticatedUserController
{
    public function store(Request $request): ApiResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email:filter'],
            'password' => ['required', 'string'],
        ]);

        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ])->status(HttpFoundationResponse::HTTP_TOO_MANY_REQUESTS);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            $this->throwFailedAuthenticationException();
        }

        $accessToken = $user->createToken($request->ip())->plainTextToken;

        return new ApiResponse(
            'Login successful', [
                'access_token' => $accessToken,
                'user' => new UserResource($user),
            ],
            201
        );
    }

    public function destroy(Request $request): Response
    {
        /** @var User */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->noContent();
    }

    private function throwFailedAuthenticationException()
    {
        throw ValidationException::withMessages([
            'email' => [__('auth.failed')],
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }
}
