<?php

namespace App\Policies\v1;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contact $contact): bool
    {
        return $contact->created_by === $user->id ||
            ($contact->team && $contact->team->hasMember($user));
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        return $contact->created_by === $user->id ||
            ($contact->team && $contact->team->userCan($user, 'manage_contacts'));
    }

    public function delete(User $user, Contact $contact): bool
    {
        return $this->update($user, $contact);
    }
}
