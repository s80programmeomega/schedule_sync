<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'member_type' => fake()->randomElement(['App\Models\User', 'App\Models\Contact']),
            'member_id' => User::factory(),
            'role' => fake()->randomElement(['member', 'admin']),
            'joined_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'added_by' => User::factory(),
        ];
    }

    public function userMember(): static
    {
        return $this->state([
            'member_type' => 'App\Models\User',
            'member_id' => User::factory(),
        ]);
    }

    public function contactMember(): static
    {
        return $this->state([
            'member_type' => 'App\Models\Contact',
            'member_id' => \App\Models\Contact::factory(),
        ]);
    }
}
