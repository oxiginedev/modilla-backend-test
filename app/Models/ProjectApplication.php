<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $project_id
 * @property-read int $freelancer_id
 * @property-read string $cover_letter_url
 * @property-read ?int $bid_amount
 * @property-read ?CarbonImmutable $viewed_at
 * @property-read ?CarbonImmutable $accepted_at
 * @property-read ?CarbonImmutable $rejected_at
 * @property-read ?string $rejection_reason
 * @property-read Project $project
 */
final class ProjectApplication extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectApplicationFactory> */
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the freelancer that owns the application
     *
     * @return BelongsTo<User, $this>
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasBeenViewed(): bool
    {
        return $this->viewed_at !== null;
    }
}
