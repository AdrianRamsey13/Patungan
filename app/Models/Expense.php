<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    protected $fillable = [
        'event_id',
        'paid_by',
        'amount',
        'description',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // User yang nalangin / bayar duluan
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    // Bagian per orang dari expense ini
    public function sharePerPerson(): int
    {
        $count = $this->splits()->count();
        return $count > 0 ? (int) round($this->amount / $count) : $this->amount;
    }

    // Auto-buat split rata untuk semua member event
    public function createEvenSplits(): void
    {
        $memberIds = $this->event->members()->pluck('users.id');
        $share = (int) round($this->amount / $memberIds->count());

        foreach ($memberIds as $userId) {
            ExpenseSplit::create([
                'expense_id'  => $this->id,
                'user_id'     => $userId,
                'amount_owed' => $share,
                'is_paid'     => $userId === $this->paid_by, // payer dianggap sudah bayar
            ]);
        }
    }
}
