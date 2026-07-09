<?php

namespace App\Policies;

use App\Enum\Roles;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isStaff();
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }

    public function update(User $user, User $model): bool
    {
        // isStaff() is true for admins too, but isAdmin() must stay explicit here:
        // it's an exemption from the "staff can't touch admin accounts" restriction,
        // not a tier check, so it can't be folded into the isStaff() branch.
        return $user->isAdmin() || ($user->isStaff() && $model->role !== Roles::Admin);
    }

    public function assignRole(User $user, Roles $role): bool
    {
        return $user->isAdmin() || $role !== Roles::Admin;
    }
}
