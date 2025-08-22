<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run timezone seeder first
        $this->call([TimezoneSeeder::class]);

        // Create a test user
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@email.com',
            'username' => 'admin',
            'timezone' => 'UTC',
        ]);

        // Create additional random users
        User::factory(5)->create();
        $this->call([
            EventTypeSeeder::class,
            AvailabilitySeeder::class,
            BookingSeeder::class,
        ]);
    }
}
