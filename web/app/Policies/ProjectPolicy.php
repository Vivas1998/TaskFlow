<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $project->members()->whereKey($user->id)->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return ! $project->isArchived() && $project->isOwner($user);
    }

    public function archive(User $user, Project $project): bool
    {
        return ! $project->isArchived() && $project->isOwner($user);
    }

    public function restore(User $user, Project $project): bool
    {
        return $project->isArchived() && $project->isOwner($user);
    }

    public function manageMembers(User $user, Project $project): bool
    {
        return ! $project->isArchived() && $project->isOwner($user);
    }

    public function createTask(User $user, Project $project): bool
    {
        return ! $project->isArchived() && $this->view($user, $project);
    }

    public function createEvent(User $user, Project $project): bool
    {
        return ! $project->isArchived() && $this->view($user, $project);
    }
}
