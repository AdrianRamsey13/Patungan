<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMember extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',       // null jika guest
        'guest_name',    // null jika user terdaftar
        'joined_at',
    ];

    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Apakah ini tamu non-user
    public function isGuest(): bool
    {
        return $this->user_id === null && $this->guest_name !== null;
    }

    // Nama tampil — nama user atau nama tamu
    public function displayName(): string
    {
        return $this->isGuest()
            ? $this->guest_name
            : ($this->user?->name ?? 'Unknown');
    }

    // String identifier unik untuk pairing di settlements
    public function identifier(): string
    {
        return $this->isGuest()
            ? "guest:{$this->id}"
            : "user:{$this->user_id}";
    }

    // Inisial untuk avatar
    public function initials(): string
    {
        $name  = $this->displayName();
        $words = explode(' ', trim($name));
        return count($words) >= 2
            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
            : strtoupper(substr($name, 0, 2));
    }
}
