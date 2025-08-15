<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public function handle(array $input): User
    {
        return DB::transaction(function () use ($input): User {
            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'role' => UserRole::from($input['role']),
                'password' => Hash::make($input['password']),
            ]);
        });
    }
}
