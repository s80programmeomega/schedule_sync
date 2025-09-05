<?php

namespace App\Policies\v1;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Group $group): bool
    {
        return $group->created_by === $user->id ||
            ($group->team && $group->team->hasMember($user));
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Group $group): bool
    {
        return $group->created_by === $user->id ||
            ($group->team && $group->team->userCan($user, 'manage_contacts'));
    }

    public function delete(User $user, Group $group): bool
    {
        return $this->update($user, $group);
    }
}
