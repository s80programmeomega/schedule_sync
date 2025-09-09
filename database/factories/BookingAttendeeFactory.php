<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingAttendeeFactory extends Factory
{
    public function definition(): array
    {
        $attendeeType = fake()->randomElement(['App\Models\User', 'App\Models\Contact']);

        return [
            'booking_id' => Booking::factory(),
            'attendee_type' => $attendeeType,
            'attendee_id' => $attendeeType === 'App\Models\User' ? User::factory() : Contact::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional(0.6)->phoneNumber(),
            'role' => fake()->randomElement(['organizer', 'required', 'optional']),
            'status' => fake()->randomElement(['pending', 'accepted', 'declined', 'tentative']),
            'responded_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
            'response_notes' => fake()->optional(0.3)->sentence(),
            'email_notifications' => fake()->boolean(80),
            'sms_notifications' => fake()->boolean(30),
        ];
    }

    public function organizer(): static
    {
        return $this->state([
            'role' => 'organizer',
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    public function required(): static
    {
        return $this->state(['role' => 'required']);
    }

    public function optional(): static
    {
        return $this->state(['role' => 'optional']);
    }

    public function accepted(): static
    {
        return $this->state([
            'status' => 'accepted',
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function declined(): static
    {
        return $this->state([
            'status' => 'declined',
            'responded_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'response_notes' => fake()->sentence(),
        ]);
    }
}
