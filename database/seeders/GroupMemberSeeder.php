<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    public function run(): void
    {
        // Group members are already created in GroupSeeder
        // This seeder is kept for consistency but delegates to GroupSeeder
        $this->command->info('Group members are seeded via GroupSeeder');
    }
}
