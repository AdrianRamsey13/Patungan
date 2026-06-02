<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteEntry;
use Illuminate\Http\Request;

class NoteEntryController extends Controller
{
    public function create(Note $note, string $entryType)
    {
        abort_if($note->user_id !== (int) auth()->id(), 403);
        abort_if(!in_array($entryType, ['tambah', 'bayar']), 404);

        return view('notes.entries.create', compact('note', 'entryType'));
    }

    public function store(Request $request, Note $note)
    {
        abort_if($note->user_id !== (int) auth()->id(), 403);

        $data = $request->validate([
            'date'        => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'integer', 'min:1'],
            'entry_type'  => ['required', 'in:tambah,bayar'],
        ]);

        NoteEntry::create([...$data, 'note_id' => $note->id]);

        $label = $data['entry_type'] === 'tambah' ? $note->addLabel() : $note->payLabel();

        return redirect()->route('notes.show', $note)
            ->with('success', "{$label} berhasil dicatat.");
    }

    public function destroy(Note $note, NoteEntry $entry)
    {
        abort_if($note->user_id !== (int) auth()->id(), 403);
        abort_if($entry->note_id !== $note->id, 404);

        $entry->delete();

        return back()->with('success', 'Entri dihapus.');
    }
}
