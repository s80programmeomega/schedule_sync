<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->randomNumber(3),
            'description' => fake()->optional(0.7)->sentence(),
            'timezone' => fake()->randomElement([
                'UTC',
                'America/New_York',
                'America/Los_Angeles',
                'America/Chicago',
                'Europe/London',
                'Asia/Tokyo'
            ]),
            'owner_id' => User::factory(),
            'is_active' => fake()->boolean(95),
            'plan' => fake()->randomElement(['free', 'pro', 'enterprise']),
            'max_members' => fake()->randomElement([5, 10, 25, 50]),
            'settings' => [
                'booking_window_days' => fake()->numberBetween(7, 365),
                'require_confirmation' => fake()->boolean(30),
                'allow_guests' => fake()->boolean(70),
            ],
        ];
    }

    public function free(): static
    {
        return $this->state(['plan' => 'free', 'max_members' => 5]);
    }

    public function pro(): static
    {
        return $this->state(['plan' => 'pro', 'max_members' => 25]);
    }

    public function enterprise(): static
    {
        return $this->state(['plan' => 'enterprise', 'max_members' => 100]);
    }
}
