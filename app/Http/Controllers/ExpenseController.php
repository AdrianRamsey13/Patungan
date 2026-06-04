<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMember;
use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'description'      => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'integer', 'min:1'],
            'payer_member_id'  => ['required', 'integer', 'exists:event_members,id'],
        ]);

        // Validasi payer adalah member event ini
        $payerMember = EventMember::where('id', $data['payer_member_id'])
            ->where('event_id', $event->id)
            ->firstOrFail();

        $expense = DB::transaction(function () use ($data, $event, $payerMember) {
            $expense = $event->expenses()->create([
                'paid_by'          => $payerMember->isGuest() ? null : $payerMember->user_id,
                'guest_payer_name' => $payerMember->isGuest() ? $payerMember->guest_name : null,
                'amount'           => $data['amount'],
                'description'      => $data['description'],
            ]);

            $members = $event->eventMembers()->with('user')->get();
            $this->splits->createSplits($expense, $members);

            return $expense;
        });

        return redirect()->route('events.show', $event)
            ->with('success', "Pengeluaran \"{$expense->description}\" berhasil ditambahkan.");
    }
}
