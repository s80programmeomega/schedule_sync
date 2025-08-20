<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = $this->faker->numberBetween(4, 17);
        $startMinute = $this->faker->randomElement([0, 15, 30, 45]);
        $startTime = Carbon::createFromTime($startHour, $startMinute);

        $duration = $this->faker->randomElement([7, 10, 12]);
        $endTime = (clone $startTime)->addHours($duration)->format('H:i');

        return [
            'user_id' => User::factory(),
            'availability_date' => $this->faker->dateTimeBetween('now', '+4 months')->format('Y-m-d'),
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime,
            'is_available' => $this->faker->boolean(72),
        ];
    }
}
