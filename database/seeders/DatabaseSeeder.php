<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\EventType;
use App\Models\Booking;
use App\Models\BookingAttendee;
use App\Models\Timezone;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Contact;
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
        User::factory(random_int(4, 9))->create();
        $this->command->info('✅ Additional users created');

        // 4. Seed teams and team members
        // Team::factory(random_int(3, 10))->create();
        $this->call([TeamSeeder::class]);
        $this->command->info('✅ Teams and members seeded');

        // 5. Seed contacts (depends on users and teams)
        // Contact::factory(random_int(15, 75))->create();
        $this->call([ContactSeeder::class]);
        $this->command->info('✅ Contacts seeded');

        // 6. Seed groups (depends on teams and contacts)
        // Group::factory(random_int(3, 10))->create();
        $this->call([GroupSeeder::class]);
        $this->command->info('✅ Groups seeded');

        // 7. Seed event types (depends on users and teams)
        // EventType::factory(random_int(7, 15))->create();
        $this->call([EventTypeSeeder::class]);
        $this->command->info('✅ Event types seeded');

        // 8. Seed availability (depends on users)
        // Availability::factory(random_int(15, 35))->create();
        $this->call([AvailabilitySeeder::class]);
        $this->command->info('✅ Availability seeded');

        // 9. Seed bookings (depends on event types and contacts)
        // Booking::factory(random_int(30, 75))->create();
        $this->call([BookingSeeder::class]);
        $this->command->info('✅ Bookings seeded');

        // 10. Seed booking attendees (depends on bookings)
        // BookingAttendee::factory(random_int(25, 100))->create();
        $this->call([BookingAttendeeSeeder::class]);
        $this->command->info('✅ Booking attendees seeded');


        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Users', User::count()],
                ['Teams', Team::count()],
                ['Team Members', TeamMember::count()],
                ['Contacts', Contact::count()],
                ['Groups', Group::count()],
                ['Group Members', GroupMember::count()],
                ['Event Types', EventType::count()],
                ['Bookings', Booking::count()],
                ['Bookings Attendees', BookingAttendee::count()],
                ['Availabilities', Availability::count()],
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
