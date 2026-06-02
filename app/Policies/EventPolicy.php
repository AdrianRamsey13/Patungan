<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    // Bisa lihat jika user adalah anggota event
    public function view(User $user, Event $event): bool
    {
        return $event->members()->where('users.id', $user->id)->exists();
    }

    // Hanya creator yang boleh edit/hapus
    public function update(User $user, Event $event): bool
    {
        return $event->created_by === $user->id;
    }

    public function delete(User $user, Event $event): bool
    {
        return $event->created_by === $user->id;
    }
}
