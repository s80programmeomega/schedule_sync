<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingAttendee;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingAttendeeSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            $addedAttendees = collect();

            // Add organizer (booking owner)
            BookingAttendee::firstOrCreate([
                'booking_id' => $booking->id,
                'attendee_type' => 'App\Models\User',
                'attendee_id' => $booking->user_id,
            ], [
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'role' => 'organizer',
                'status' => 'accepted',
                'responded_at' => now(),
                'email_notifications' => true,
                'sms_notifications' => false,
            ]);

            $addedAttendees->push(['type' => 'App\Models\User', 'id' => $booking->user_id]);

            // Add 1-3 additional attendees (mix of users and contacts)
            $attendeeCount = fake()->numberBetween(1, 3);
            $attempts = 0;
            $maxAttempts = $attendeeCount * 3; // Prevent infinite loops

            while ($addedAttendees->count() < $attendeeCount + 1 && $attempts < $maxAttempts) {
                $attempts++;
                $isUser = fake()->boolean(60);

                if ($isUser) {
                    $user = User::where('id', '!=', $booking->user_id)->inRandomOrder()->first();
                    if ($user && !$addedAttendees->contains(fn($a) => $a['type'] === 'App\Models\User' && $a['id'] === $user->id)) {
                        BookingAttendee::create([
                            'booking_id' => $booking->id,
                            'attendee_type' => 'App\Models\User',
                            'attendee_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => fake()->randomElement(['required', 'optional']),
                            'status' => fake()->randomElement(['pending', 'accepted', 'declined', 'tentative']),
                            'responded_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
                            'email_notifications' => fake()->boolean(80),
                            'sms_notifications' => fake()->boolean(30),
                        ]);
                        $addedAttendees->push(['type' => 'App\Models\User', 'id' => $user->id]);
                    }
                } else {
                    $contact = Contact::inRandomOrder()->first();
                    if ($contact && !$addedAttendees->contains(fn($a) => $a['type'] === 'App\Models\Contact' && $a['id'] === $contact->id)) {
                        BookingAttendee::create([
                            'booking_id' => $booking->id,
                            'attendee_type' => 'App\Models\Contact',
                            'attendee_id' => $contact->id,
                            'name' => $contact->name,
                            'email' => $contact->email,
                            'phone' => $contact->phone,
                            'role' => fake()->randomElement(['required', 'optional']),
                            'status' => fake()->randomElement(['pending', 'accepted', 'declined', 'tentative']),
                            'responded_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
                            'email_notifications' => fake()->boolean(80),
                            'sms_notifications' => fake()->boolean(30),
                        ]);
                        $addedAttendees->push(['type' => 'App\Models\Contact', 'id' => $contact->id]);
                    }
                }
            }
        }

        $this->command->info('Created attendees for ' . $bookings->count() . ' bookings');
    }
}
