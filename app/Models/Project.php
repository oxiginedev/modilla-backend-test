<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read int $id
 * @property-read int $client_id
 * @property-read string $title
 * @property-read string $description
 * @property-read int $budget_min_amount
 * @property-read int $budget_max_amount
 * @property-read ProjectStatus $status
 * @property-read CarbonImmutable $created_at
 * @property-read CarbonImmutable $updated_at
 * @property-read ?CarbonImmutable $application_closes_at
 * @property-read User $owner
 * @property-read Collection<ProjectApplication> $applications
 */
final class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    /**
     * Get the client that owns the project
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Gets all applications to this projects
     *
     * @return HasMany<ProjectApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(ProjectApplication::class);
    }

    /**
     * Checks if projects is still accepting applications
     */
    public function isAcceptingApplications(): bool
    {
        return $this->status === ProjectStatus::OPEN &&
            ($this->application_closes_at === null ||
                $this->application_closes_at < now());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'application_closes_at' => 'datetime',
        ];
    }
}
