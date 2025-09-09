<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupMemberFactory extends Factory
{
    public function definition(): array
    {
        $memberType = fake()->randomElement(['App\Models\User', 'App\Models\Contact']);

        return [
            'group_id' => Group::factory(),
            'member_type' => $memberType,
            'member_id' => $memberType === 'App\Models\User' ? User::factory() : Contact::factory(),
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
            'member_id' => Contact::factory(),
        ]);
    }
}
