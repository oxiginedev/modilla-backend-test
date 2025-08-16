<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
final class ProjectFactory extends Factory
{
    protected static $title;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => self::$title ?? fake()->sentence(),
            'description' => fake()->paragraph(),
            'client_id' => User::factory(),
            'budget_min_amount' => random_int(999, 2000),
            'budget_max_amount' => random_int(2001, 10000),
            'status' => fake()->randomElement([
                ProjectStatus::DRAFT,
                ProjectStatus::OPEN,
                ProjectStatus::CLOSED,
            ]),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::CLOSED,
        ]);
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::OPEN,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProjectStatus::DRAFT,
        ]);
    }
}
