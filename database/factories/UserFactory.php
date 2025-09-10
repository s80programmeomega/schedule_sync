<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Timezone;
use App\Models\Team;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => null,
            'password' => static::$password ??= Hash::make('admin'),
            'remember_token' => Str::random(10),
            'username' => fake()->unique()->userName(),
            'timezone_id' => Timezone::inRandomOrder()->first()->id,
            'bio' => fake()->optional()->sentence(10),
            'avatar' => fake()->optional()->imageUrl(200, 200, 'people'),
            'default_team_id' => null,

        ];

        // return [
        //     'name' => fake()->name(),
        //     'email' => fake()->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'password' => static::$password ??= Hash::make('password'),
        //     'remember_token' => Str::random(10),
        // ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function withDefaultTeam(): static
    {
        return $this->afterCreating(function ($user) {
            $team = Team::factory()->create(['owner_id' => $user->id]);
            $user->update(['default_team_id' => $team->id]);
        });
    }
}
