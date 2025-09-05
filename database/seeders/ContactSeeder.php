<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $teams = Team::all();

        foreach ($users as $user) {
            // Personal contacts (3-7 per user)
            Contact::factory(fake()->numberBetween(3, 7))->create([
                'created_by' => $user->id,
                'team_id' => null,
            ]);

            // Team contacts for teams where user is owner or admin
            $userTeams = $teams->filter(function ($team) use ($user) {
                return $team->owner_id === $user->id ||
                    $team->members()->where('user_id', $user->id)
                    ->whereIn('role', ['owner', 'admin'])
                    ->where('status', 'active')
                    ->exists();
            });

            foreach ($userTeams as $team) {
                Contact::factory(fake()->numberBetween(2, 5))->create([
                    'created_by' => $user->id,
                    'team_id' => $team->id,
                ]);
            }
        }

        $this->command->info('Created contacts for ' . $users->count() . ' users');
    }
}
