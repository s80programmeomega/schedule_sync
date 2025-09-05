<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'user_id' => User::factory(),
            'role' => fake()->randomElement(['member', 'admin', 'viewer']),
            'status' => 'active',
            'joined_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'invited_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'invited_by' => User::factory(),
            'permissions' => null,
        ];
    }

    public function owner(): static
    {
        return $this->state([
            'role' => 'owner',
            'status' => 'active',
            'joined_at' => now(),
            'invited_by' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function member(): static
    {
        return $this->state(['role' => 'member']);
    }

    public function viewer(): static
    {
        return $this->state(['role' => 'viewer']);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => 'pending',
            'joined_at' => null,
            'invitation_token' => Str::random(64),
            'invitation_expires_at' => fake()->dateTimeBetween('now', '+7 days'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
