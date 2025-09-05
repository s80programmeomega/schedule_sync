<?php

namespace Database\Seeders;

use App\Models\Timezone;
use App\Models\User;

namespace Database\Seeders;

use App\Models\Timezone;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed the application's database.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // 1. Seed timezones first (required for users)
        $this->call([TimezoneSeeder::class]);
        $this->command->info('✅ Timezones seeded');

        // 2. Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'username' => 'admin',
            'timezone_id' => Timezone::where('name', 'UTC')->first()?->id ?? 1,
        ]);
        $this->command->info('✅ Admin user created');

        // 3. Create additional users
        User::factory(35)->create();
        $this->command->info('✅ Additional users created');

        // 4. Seed teams and team members
        $this->call([TeamSeeder::class]);
        $this->command->info('✅ Teams and members seeded');

        // 5. Seed contacts (depends on users and teams)
        $this->call([ContactSeeder::class]);
        $this->command->info('✅ Contacts seeded');

        // 6. Seed groups (depends on teams and contacts)
        $this->call([GroupSeeder::class]);
        $this->command->info('✅ Groups seeded');

        // 7. Seed event types (depends on users and teams)
        $this->call([EventTypeSeeder::class]);
        $this->command->info('✅ Event types seeded');

        // 8. Seed availability (depends on users)
        $this->call([AvailabilitySeeder::class]);
        $this->command->info('✅ Availability seeded');

        // 9. Seed bookings (depends on event types and contacts)
        $this->call([BookingSeeder::class]);
        $this->command->info('✅ Bookings seeded');

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Users', User::count()],
                ['Teams', \App\Models\Team::count()],
                ['Team Members', \App\Models\TeamMember::count()],
                ['Contacts', \App\Models\Contact::count()],
                ['Groups', \App\Models\Group::count()],
                ['Event Types', \App\Models\EventType::count()],
                ['Bookings', \App\Models\Booking::count()],
            ]
        );
    }



    // public function run(): void
    // {
    //     // Run timezone seeder first
    //     // $this->call([TimezoneSeeder::class]);

    //     $this->call([
    //         TimezoneSeeder::class,
    //         TeamSeeder::class,
    //         ContactSeeder::class,
    //         EventTypeSeeder::class,
    //         AvailabilitySeeder::class,
    //         BookingSeeder::class,
    //     ]);

    //     // Create a test user
    //     User::factory()->create([
    //         'name' => 'admin',
    //         'email' => 'admin@email.com',
    //         'username' => 'admin',
    //         'timezone_id' => Timezone::inRandomOrder()->first()->id,
    //     ]);

    //     // Create additional random users
    //     User::factory(5)->create();
    //     $this->call([
    //         EventTypeSeeder::class,
    //         AvailabilitySeeder::class,
    //         BookingSeeder::class,
    //     ]);
    // }
}
