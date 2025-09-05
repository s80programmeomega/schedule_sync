<?php

namespace App\Policies\v1;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    public function viewAny(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    public function view(User $user, TeamMember $teamMember): bool
    {
        return $teamMember->team->hasMember($user);
    }

    public function create(User $user, Team $team): bool
    {
        return $team->userCan($user, 'manage_members');
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        return $teamMember->team->userCan($user, 'manage_members')
            && $teamMember->role !== 'owner';
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        return $teamMember->team->userCan($user, 'manage_members')
            && $teamMember->role !== 'owner';
    }
}
