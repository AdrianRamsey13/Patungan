<?php

namespace App\Models;

use App\Services\ExpenseSplitService;
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

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    // Bagian per orang rata-rata
    public function sharePerPerson(): int
    {
        $count = $this->splits()->count();
        return $count > 0 ? (int) round($this->amount / $count) : $this->amount;
    }

    // Buat splits rata menggunakan service
    public function createEvenSplits(): void
    {
        $memberIds = $this->event->members()->pluck('users.id');
        app(ExpenseSplitService::class)->createSplits($this, $memberIds);
    }
}
