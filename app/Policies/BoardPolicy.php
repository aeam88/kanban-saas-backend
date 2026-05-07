<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $board->project->workspace->users->contains($user);
    }

    public function create(User $user): bool
    {
        return true; 
    }

    public function update(User $user, Board $board): bool
    {
        return $board->project->workspace->users->contains($user);
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->project->workspace->users->contains($user);
    }
}
