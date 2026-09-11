<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(User $user, Event $event): bool
    {
        return $event->project->members()->whereKey($user->id)->exists();
    }

    public function update(User $user, Event $event): bool
    {
        return ! $event->project->isArchived()
            && $event->project->members()->whereKey($user->id)->exists();
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }

    public function restore(User $user, Event $event): bool
    {
        return ! $event->project->isArchived()
            && $event->project->members()->whereKey($user->id)->exists();
    }
}
