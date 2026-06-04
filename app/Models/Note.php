<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NoteEntry> $addEntries
 * @property-read int|null $add_entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NoteEntry> $entries
 * @property-read int|null $entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NoteEntry> $payEntries
 * @property-read int|null $pay_entries_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Note whereUserId($value)
 * @mixin \Eloquent
 */
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
