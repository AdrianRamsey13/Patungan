<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $event_id
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event $event
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EventMember whereUserId($value)
 * @mixin \Eloquent
 */
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
