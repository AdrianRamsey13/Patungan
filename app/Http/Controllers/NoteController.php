<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::where('user_id', auth()->id())
            ->withSum(['entries as total_added' => fn($q) => $q->where('entry_type', 'tambah')], 'amount')
            ->withSum(['entries as total_paid'  => fn($q) => $q->where('entry_type', 'bayar')], 'amount')
            ->withCount('entries')
            ->latest()
            ->get();

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type'        => ['required', 'in:hutang,piutang'],
        ]);

        $note = Note::create([...$data, 'user_id' => auth()->id()]);

        return redirect()->route('notes.show', $note)
            ->with('success', "Notes \"{$note->name}\" berhasil dibuat.");
    }

    public function show(Note $note)
    {
        $this->authorizeNote($note);

        $addEntries = $note->addEntries()->orderByDesc('date')->orderByDesc('created_at')->get();
        $payEntries = $note->payEntries()->orderByDesc('date')->orderByDesc('created_at')->get();

        $totalAdded = (int) $addEntries->sum('amount');
        $totalPaid  = (int) $payEntries->sum('amount');
        $balance    = $totalAdded - $totalPaid;

        return view('notes.show', compact(
            'note', 'addEntries', 'payEntries',
            'totalAdded', 'totalPaid', 'balance'
        ));
    }

    public function destroy(Note $note)
    {
        $this->authorizeNote($note);
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Notes dihapus.');
    }

    private function authorizeNote(Note $note): void
    {
        abort_if($note->user_id !== (int) auth()->id(), 403);
    }
}
