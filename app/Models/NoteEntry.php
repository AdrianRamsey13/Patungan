<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $note_id
 * @property \Illuminate\Support\Carbon $date
 * @property string $description
 * @property int $amount
 * @property string $entry_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Note $note
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereEntryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NoteEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
