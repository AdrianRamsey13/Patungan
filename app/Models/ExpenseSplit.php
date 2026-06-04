<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $expense_id
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property int $amount_owed
 * @property int $amount_paid
 * @property bool $is_paid
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Expense $expense
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereAmountOwed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereGuestName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExpenseSplit whereUserId($value)
 * @mixin \Eloquent
 */
class ExpenseSplit extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',       // null jika guest split
        'guest_name',    // null jika user split
        'amount_owed',
        'amount_paid',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isGuestSplit(): bool
    {
        return $this->user_id === null && $this->guest_name !== null;
    }

    public function displayName(): string
    {
        if ($this->isGuestSplit()) {
            return $this->guest_name;
        }
        return $this->user?->name ?? 'Unknown';
    }

    public function remaining(): int
    {
        return max(0, $this->amount_owed - $this->amount_paid);
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'amount_paid' => $this->amount_owed,
            'is_paid'     => true,
            'paid_at'     => now(),
        ]);
    }
}
