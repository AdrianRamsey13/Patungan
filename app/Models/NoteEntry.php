<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteEntry extends Model
{
    protected $fillable = ['note_id', 'date', 'description', 'amount', 'entry_type'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    // Apakah ini entry penambahan (bukan pembayaran)
    public function isAdd(): bool
    {
        return $this->entry_type === 'tambah';
    }
}
