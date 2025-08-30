<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\EventType;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventType::all()->each(function ($eventType) {
            Booking::factory(rand(7, 30))->create([
                'event_type_id' => $eventType->id,
                'user_id' => $eventType->user_id,
            ]);
        });
    }
}
