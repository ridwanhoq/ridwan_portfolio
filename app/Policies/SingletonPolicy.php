<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Fixed records (SEO pages, homepage sections) can be edited but not created
 * or deleted from the admin panel.
 */
class SingletonPolicy extends ContentPolicy
{
    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }
}
