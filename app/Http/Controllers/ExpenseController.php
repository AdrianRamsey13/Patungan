<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create(Event $event)
    {
        $this->authorize('view', $event);
        return view('expenses.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('view', $event);

        $data = $request->validate([
            'amount'      => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:255'],
            'splits'      => ['nullable', 'array'],   // optional custom split: [user_id => amount]
            'splits.*'    => ['integer', 'min:0'],
        ]);

        $expense = $event->expenses()->create([
            'paid_by'     => auth()->id(),
            'amount'      => $data['amount'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['splits'])) {
            // Custom split
            foreach ($data['splits'] as $userId => $amount) {
                $expense->splits()->create([
                    'user_id'     => $userId,
                    'amount_owed' => $amount,
                    'is_paid'     => (int) $userId === auth()->id(),
                ]);
            }
        } else {
            // Split rata
            $expense->createEvenSplits();
        }

        return redirect()->route('events.show', $event)->with('success', 'Pengeluaran ditambahkan.');
    }
}
