<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\User;

use App\Actions\User\CreateUser;
use App\Models\User;
use Tests\TestCase;

final class CreateUserTest extends TestCase
{
    public function test_can_create_user(): void
    {
        $action = new CreateUser;

        $user = $action->handle([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => fake()->randomElement(['client', 'freelancer']),
            'password' => 'password',
        ]);

        $this->assertInstanceOf(User::class, $user);
    }
}
