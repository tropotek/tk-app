<?php

namespace Demo\Policies;

use App\Models\User;
use Demo\Models\Idea;

class IdeaPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function update(User $user, Idea $idea): bool
    {
        return $user->is($idea->user);
    }
}
