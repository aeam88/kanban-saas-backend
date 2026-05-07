<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->workspace->owner_id || $project->workspace->users->contains($user);
    }

    public function create(User $user): bool
    {
        return true; 
    }

    public function update(User $user, Project $project): bool
    {
        return $project->workspace->users->contains($user);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->workspace->owner_id === $user->id;
    }
}
