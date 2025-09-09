<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        // Team members are already created in TeamSeeder
        // This seeder is kept for consistency but delegates to TeamSeeder
        $this->command->info('Team members are seeded via TeamSeeder');
    }
}
