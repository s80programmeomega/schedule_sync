<?php

namespace App\Policies\v1;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Team $team): bool
    {
        return $team->userCan($user, 'manage_team_settings');
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->userHasRole($user, 'owner');
    }

    public function viewMembers(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    public function manageMembers(User $user, Team $team): bool
    {
        return $team->userCan($user, 'manage_members');
    }

    public function manageContacts(User $user, Team $team): bool
    {
        return $team->userCan($user, 'manage_contacts');
    }
}
