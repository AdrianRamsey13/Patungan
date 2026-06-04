<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ExpenseSplit;

class MyReceivableController extends Controller
{
    public function index()
    {
        $userId = (int) auth()->id();

        // Semua split yang belum dibayar ke user (user = creditor/payer)
        $splits = ExpenseSplit::with([
                'expense.event.eventMembers.user',
                'user',
            ])
            ->where(fn($q) => $q->where('user_id', '!=', $userId)->orWhereNull('user_id'))
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where('paid_by', $userId)
                  ->whereHas('event', fn($q2) => $q2->where('status', 'open'))
            )
            ->get();

        // Event cards
        $eventIds = $splits->pluck('expense.event_id')->unique()->values();
        $events   = Event::whereIn('id', $eventIds)
            ->with(['eventMembers', 'expenses.splits'])
            ->get();

        // Tabel — tiap row = satu debtor yang belum bayar ke user
        $tableRows = $splits->map(function (ExpenseSplit $split) use ($userId) {
            $expense        = $split->expense;
            $event          = $expense->event;
            $creditorMember = $event->eventMembers->firstWhere('user_id', $userId);
            $debtorMember   = $split->isGuestSplit()
                ? $event->eventMembers->firstWhere('guest_name', $split->guest_name)
                : $event->eventMembers->firstWhere('user_id', $split->user_id);

            return [
                'split'           => $split,
                'event'           => $event,
                'expense'         => $expense,
                'debtor_name'     => $split->user?->name ?? $split->guest_name ?? 'Tamu',
                'is_guest'        => $split->isGuestSplit(),
                'creditor_member' => $creditorMember,
                'debtor_member'   => $debtorMember,
                'amount'          => $split->remaining(),
            ];
        })->sortBy('event.name')->values();

        $totalReceive = $tableRows->sum('amount');

        return view('my-receivables.index', compact('events', 'tableRows', 'totalReceive'));
    }
}
