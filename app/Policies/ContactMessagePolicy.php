<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

/**
 * Messages come from visitors: admins can read, mark and delete them,
 * but never create or edit their content.
 */
class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->isAdmin();
    }
}
