<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.7)->phoneNumber(),
            'company' => fake()->optional(0.6)->company(),
            'job_title' => fake()->optional(0.5)->jobTitle(),
            'notes' => fake()->optional(0.4)->sentence(),
            'timezone' => fake()->timezone(),
            'created_by' => User::factory(),
            'team_id' => null, // Personal contact by default
            'is_active' => fake()->boolean(95),
            'email_notifications' => fake()->boolean(80),
            'sms_notifications' => fake()->boolean(30),
            'total_bookings' => fake()->numberBetween(0, 20),
        ];
    }

    public function teamContact(): static
    {
        return $this->state(fn() => [
            'team_id' => Team::factory(),
        ]);
    }

    public function personal(): static
    {
        return $this->state(['team_id' => null]);
    }
}
