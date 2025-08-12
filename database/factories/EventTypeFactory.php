<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventType>
 */
class EventTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['30 Min Meeting', '1 Hour Consultation', 'Quick Chat', 'Strategy Session']),
            'description' => fake()->optional()->paragraph(),
            'duration' => fake()->randomElement([15, 30, 45, 60, 90]),
            'location_type' => fake()->randomElement(['zoom', 'google_meet', 'phone', 'whatsapp']),
            'location_details' => fake()->optional()->url(),
            'buffer_time_before' => fake()->randomElement([0, 5, 10, 15]),
            'buffer_time_after' => fake()->randomElement([0, 5, 10, 15]),
            'is_active' => fake()->boolean(80),
            'requires_confirmation' => fake()->boolean(30),
            'max_events_per_day' => fake()->optional()->numberBetween(1, 10),
            'color' => fake()->randomElement(['#5D5CDE', '#FF5733', '#33FF57', '#3357FF']),
        ];
    }
}
