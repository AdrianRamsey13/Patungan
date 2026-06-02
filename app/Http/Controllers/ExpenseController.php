<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    public function create(Event $event)
    {
        $this->authorize('view', $event);
        abort_if($event->status === 'closed', 403, 'Event sudah ditutup.');
        return view('expenses.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('view', $event);
        abort_if($event->status === 'closed', 403, 'Event sudah ditutup.');

        $data = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'integer', 'min:1'],
        ]);

        $expense = \Illuminate\Support\Facades\DB::transaction(function () use ($data, $event) {
            $expense = $event->expenses()->create([
                'paid_by'     => auth()->id(),
                'amount'      => $data['amount'],
                'description' => $data['description'],
            ]);

            $memberIds = $event->members()->pluck('users.id');
            $this->splits->createSplits($expense, $memberIds);

            return $expense;
        });

        return redirect()->route('events.show', $event)
            ->with('success', "Pengeluaran \"{$expense->description}\" berhasil ditambahkan.");
    }
}
