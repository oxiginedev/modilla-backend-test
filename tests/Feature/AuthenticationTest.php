<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->randomRole()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'user',
            ],
        ]);
    }

    public function test_user_cant_login_with_invalid_credentials(): void
    {
        $user = User::factory()->randomRole()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->randomRole()->create();

        Sanctum::actingAs($user);

        $response = $this->post('/auth/logout');
        $response->assertNoContent();
    }
}
