<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create teams for first 3 users
        $teamOwners = $users->take(3);

        foreach ($teamOwners as $owner) {
            $team = Team::factory()->create([
                'owner_id' => $owner->id,
                'name' => $owner->name . "'s Team",
            ]);

            // Set as default team if user doesn't have one
            if (!$owner->default_team_id) {
                $owner->update(['default_team_id' => $team->id]);
            }

            // Add 2-4 additional members to each team
            $availableUsers = $users->where('id', '!=', $owner->id)->shuffle();
            $memberCount = fake()->numberBetween(2, min(4, $availableUsers->count()));

            foreach ($availableUsers->take($memberCount) as $member) {
                TeamMember::factory()->create([
                    'team_id' => $team->id,
                    'user_id' => $member->id,
                    'role' => fake()->randomElement(['admin', 'member', 'viewer']),
                    'invited_by' => $owner->id,
                ]);
            }

            // Create 1-2 pending invitations
            $pendingCount = fake()->numberBetween(0, 2);
            if ($pendingCount > 0 && $availableUsers->count() > $memberCount) {
                $pendingUsers = $availableUsers->skip($memberCount)->take($pendingCount);
                foreach ($pendingUsers as $pendingUser) {
                    TeamMember::factory()->pending()->create([
                        'team_id' => $team->id,
                        'user_id' => $pendingUser->id,
                        'invited_by' => $owner->id,
                    ]);
                }
            }
        }

        $this->command->info('Created ' . $teamOwners->count() . ' teams with members');
    }
}
