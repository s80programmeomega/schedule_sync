<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional(0.6)->sentence(),
            'color' => fake()->hexColor(),
            'type' => fake()->randomElement(['team_members', 'contacts', 'mixed']),
            'team_id' => Team::factory(),
            'created_by' => User::factory(),
            'is_active' => fake()->boolean(90),
            'settings' => [
                'auto_add_new_members' => fake()->boolean(20),
                'notification_preferences' => fake()->randomElement(['all', 'important', 'none']),
            ],
        ];
    }

    public function teamMembers(): static
    {
        return $this->state(['type' => 'team_members']);
    }

    public function contacts(): static
    {
        return $this->state(['type' => 'contacts']);
    }

    public function mixed(): static
    {
        return $this->state(['type' => 'mixed']);
    }
}
