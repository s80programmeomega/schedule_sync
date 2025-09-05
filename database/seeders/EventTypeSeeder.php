<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;
use App\Models\User;
use App\Models\Team;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $teams = Team::all();

        foreach ($users as $user) {
            // Create personal event types
            EventType::factory(fake()->numberBetween(3, 8))->create([
                'user_id' => $user->id,
                'team_id' => null,
            ]);

            // Create team event types if user owns teams
            $ownedTeams = $teams->where('owner_id', $user->id);
            foreach ($ownedTeams as $team) {
                EventType::factory(fake()->numberBetween(2, 5))->create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                ]);
            }
        }

        $this->command->info('Created event types for ' . $users->count() . ' users');
    }
}
