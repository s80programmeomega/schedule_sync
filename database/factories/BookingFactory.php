<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EventType;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Timezone;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking_date = fake()->dateTimeBetween(startDate: "now", endDate: "+1 month")->format(format: 'Y-m-d');
        $startTime = Carbon::parse(fake()->time('H:i'));
        $duration = fake()->randomElement([30, 60, 90]);
        $endTime = (clone $startTime)->addMinutes($duration);
        $timezone_id = Timezone::inRandomOrder()->first()->id;


        return [
            'event_type_id' => EventType::factory(),
            'user_id' => User::factory(),
            'attendee_name' => fake()->name(),
            'attendee_email' => fake()->safeEmail(),
            'attendee_notes' => fake()->optional()->paragraph(),
            'booking_date' => $booking_date,
            'timezone_id' => $timezone_id,
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime->format('H:i'),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled', 'no_show']),
            'meeting_link' => fake()->optional()->url(),
            'cancellation_reason' => fake()->optional()->sentence(),
            'cancelled_at' => fake()->optional()->dateTime(),
        ];
    }
    public function scheduled(): static
    {
        return $this->state(['status' => 'scheduled']);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => 'cancelled',
            'cancellation_reason' => fake()->sentence(),
            'cancelled_at' => now(),
        ]);
    }
}
