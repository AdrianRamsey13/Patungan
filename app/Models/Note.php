<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(NoteEntry::class)->orderBy('date')->orderBy('created_at');
    }

    // Semua entri "tambah" (nambah hutang/piutang)
    public function addEntries(): HasMany
    {
        return $this->hasMany(NoteEntry::class)->where('entry_type', 'tambah');
    }

    // Semua entri "bayar" (pembayaran)
    public function payEntries(): HasMany
    {
        return $this->hasMany(NoteEntry::class)->where('entry_type', 'bayar');
    }

    // Total catatan (hutang/piutang yang ditambahkan)
    public function totalAdded(): int
    {
        return (int) $this->addEntries()->sum('amount');
    }

    // Total yang sudah dibayar
    public function totalPaid(): int
    {
        return (int) $this->payEntries()->sum('amount');
    }

    // Sisa outstanding
    public function balance(): int
    {
        return $this->totalAdded() - $this->totalPaid();
    }

    // Label kontekstual sesuai tipe note
    public function addLabel(): string
    {
        return $this->type === 'hutang' ? 'Tambah Hutang' : 'Tambah Piutang';
    }

    public function payLabel(): string
    {
        return $this->type === 'hutang' ? 'Catat Pelunasan' : 'Catat Pembayaran';
    }

    public function typeLabel(): string
    {
        return $this->type === 'hutang' ? 'Hutang' : 'Piutang';
    }
}
