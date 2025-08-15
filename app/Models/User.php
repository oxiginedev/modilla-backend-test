<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read UserRole $role
 * @property-read ?CarbonImmutable $email_verified_at
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read Collection<Project> $projects
 */
final class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Determine if the user owns the given project
     */
    public function ownsProject(Project $project): bool
    {
        return $this->id === $project->client_id;
    }

    public function hasAppliedToProject(Project $project): bool
    {
        return ProjectApplication::query()
            ->where('project_id', $project->id)
            ->where('freelancer_id', $this->id)
            ->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }
}
