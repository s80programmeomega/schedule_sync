<?php

namespace App\Policies\v1;

use App\Models\GroupMember;
use App\Models\User;

class GroupMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GroupMember $groupMember): bool
    {
        return $groupMember->group->created_by === $user->id ||
            ($groupMember->group->team && $groupMember->group->team->hasMember($user));
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, GroupMember $groupMember): bool
    {
        return $groupMember->group->created_by === $user->id ||
            ($groupMember->group->team && $groupMember->group->team->userCan($user, 'manage_contacts'));
    }

    public function delete(User $user, GroupMember $groupMember): bool
    {
        return $this->update($user, $groupMember);
    }
}
