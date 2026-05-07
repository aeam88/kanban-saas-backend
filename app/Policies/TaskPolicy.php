<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $task->board->project->workspace->users->contains($user);
    }

    public function create(User $user): bool
    {
        return true; 
    }

    public function update(User $user, Task $task): bool
    {
        return $task->board->project->workspace->users->contains($user);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->board->project->workspace->users->contains($user);
    }
}
