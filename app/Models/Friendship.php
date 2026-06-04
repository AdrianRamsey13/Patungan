<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $friend_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $receiver
 * @property-read \App\Models\User $sender
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereFriendId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Friendship whereUserId($value)
 * @mixin \Eloquent
 */
class Friendship extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    // Cek apakah dua user sudah berteman (accepted)
    public static function areFriends(int $userA, int $userB): bool
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('user_id', $userA)->where('friend_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('user_id', $userB)->where('friend_id', $userA);
        })->where('status', 'accepted')->exists();
    }
}
