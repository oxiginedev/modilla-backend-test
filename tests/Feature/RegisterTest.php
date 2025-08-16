<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => fake()->randomElement(['client', 'freelancer']),
            'password' => 'password',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'user',
            ],
        ]);
    }

    public function test_new_users_cant_register_as_admin(): void
    {
        $response = $this->post('/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $response->assertUnprocessable();
    }
}
