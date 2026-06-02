<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplit extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',
        'amount_owed',
        'amount_paid',
        'is_paid',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'is_paid'  => 'boolean',
            'paid_at'  => 'datetime',
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

    // Sisa yang belum dibayar
    public function remaining(): int
    {
        return max(0, $this->amount_owed - $this->amount_paid);
    }

    // Tandai lunas — set amount_paid = amount_owed
    public function markAsPaid(): bool
    {
        return $this->update([
            'amount_paid' => $this->amount_owed,
            'is_paid'     => true,
            'paid_at'     => now(),
        ]);
    }
}
