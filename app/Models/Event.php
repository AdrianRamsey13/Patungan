<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'created_by',
        'date_start',
        'date_end',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end'   => 'date',
        ];
    }

    // User yang membuat / nalangin event ini
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Anggota user terdaftar saja (via BelongsToMany — untuk backward compat)
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_members')
                    ->withPivot('joined_at', 'guest_name')
                    ->withTimestamps()
                    ->whereNotNull('event_members.user_id');
    }

    // Semua peserta: user + guest (sebagai EventMember records)
    public function eventMembers(): HasMany
    {
        return $this->hasMany(EventMember::class);
    }

    // Total peserta (user + guest)
    public function totalParticipantCount(): int
    {
        return $this->eventMembers()->count();
    }

    // Jumlah guest non-user
    public function guestCount(): int
    {
        return $this->eventMembers()->whereNull('user_id')->count();
    }

    // Semua pengeluaran dalam event ini
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Semua split dari semua expense dalam event ini
    public function expenseSplits(): HasManyThrough
    {
        return $this->hasManyThrough(ExpenseSplit::class, Expense::class);
    }

    // Total biaya event (sum semua expenses)
    public function totalAmount(): int
    {
        return $this->expenses()->sum('amount');
    }

    // Berapa yang sudah dibayar vs total anggota (untuk progress bar)
    public function paidCount(): int
    {
        return $this->expenseSplits()->where('is_paid', true)->distinct('user_id')->count('user_id');
    }

    // Net balance user terhadap event ini:
    // positif = bakal terima (nalangin), negatif = masih utang
    public function netBalanceFor(int $userId): int
    {
        $toReceive = ExpenseSplit::whereHas('expense', fn($q) => $q->where('event_id', $this->id)
                ->where('paid_by', $userId))
            ->where('user_id', '!=', $userId)
            ->where('is_paid', false)
            ->sum('amount_owed');

        $tooPay = ExpenseSplit::where('user_id', $userId)
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) => $q->where('event_id', $this->id)
                ->where('paid_by', '!=', $userId))
            ->sum('amount_owed');

        return $toReceive - $tooPay;
    }

    // Warna aksen Tailwind per kategori (untuk UI)
    public function accentColor(): string
    {
        return match($this->category) {
            'jalan'    => 'sky',
            'konsumsi' => 'amber',
            'acara'    => 'coral',
            'sewa'     => 'grape',
            'kado'     => 'mint',
            default    => 'coral',
        };
    }

    // Icon identifier per kategori (untuk Blade component)
    public function categoryIcon(): string
    {
        return match($this->category) {
            'jalan'    => 'travel',
            'konsumsi' => 'coffee',
            'acara'    => 'ball',
            'sewa'     => 'home',
            'kado'     => 'gift',
            default    => 'ball',
        };
    }
}
