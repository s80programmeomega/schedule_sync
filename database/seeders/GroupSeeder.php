<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Team;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            // Create 1-3 groups per team
            $groupCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $groupCount; $i++) {
                $group = Group::factory()->create([
                    'team_id' => $team->id,
                    'created_by' => $team->owner_id,
                ]);

                // Add team members to group
                $teamMembers = $team->activeMembers()->inRandomOrder()->take(fake()->numberBetween(2, 5))->get();
                foreach ($teamMembers as $member) {
                    GroupMember::factory()->create([
                        'group_id' => $group->id,
                        'member_type' => 'App\Models\User',
                        'member_id' => $member->user_id,
                        'added_by' => $team->owner_id,
                    ]);
                }

                // Add some contacts if group type allows
                if (in_array($group->type, ['contacts', 'mixed'])) {
                    $teamContacts = Contact::where('team_id', $team->id)
                        ->inRandomOrder()
                        ->take(fake()->numberBetween(1, 3))
                        ->get();

                    foreach ($teamContacts as $contact) {
                        GroupMember::factory()->create([
                            'group_id' => $group->id,
                            'member_type' => 'App\Models\Contact',
                            'member_id' => $contact->id,
                            'added_by' => $team->owner_id,
                        ]);
                    }
                }
            }
        }

        $this->command->info('Created groups for ' . $teams->count() . ' teams');
    }
}
