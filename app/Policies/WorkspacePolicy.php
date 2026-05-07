<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class WorkspacePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Workspace $workspace): bool
    {
        return $user->id === $workspace->owner_id || $workspace->users->contains($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id || 
               $workspace->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }

    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id;
    }

    public function invite(User $user, Workspace $workspace): bool
    {
        return $workspace->owner_id === $user->id || 
               $workspace->users()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }
}
