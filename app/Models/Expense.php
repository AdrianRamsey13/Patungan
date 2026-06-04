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
        'paid_by',            // null jika guest yang nalangin
        'guest_payer_name',   // null jika user terdaftar yang nalangin
        'amount',
        'description',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Payer user (null jika guest yang nalangin)
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function splits(): HasMany
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function isGuestPayer(): bool
    {
        return $this->paid_by === null && $this->guest_payer_name !== null;
    }

    // Nama payer — works untuk user dan guest
    public function payerName(): string
    {
        if ($this->paid_by) {
            return $this->payer?->name ?? 'Unknown';
        }
        return $this->guest_payer_name ?? 'Unknown';
    }

    // Cek apakah EventMember tertentu adalah payer expense ini
    public function isPayerMember(EventMember $member): bool
    {
        if ($member->isGuest()) {
            return $this->guest_payer_name === $member->guest_name;
        }
        return $this->paid_by === $member->user_id;
    }

    public function sharePerPerson(): int
    {
        $count = $this->splits()->count();
        return $count > 0 ? (int) round($this->amount / $count) : $this->amount;
    }

    // Split rata ke semua member event (termasuk guest)
    public function createEvenSplits(): void
    {
        $members = $this->event->eventMembers()->with('user')->get();
        app(ExpenseSplitService::class)->createSplits($this, $members);
    }
}
