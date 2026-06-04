<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ExpenseSplit;

class MyDebtController extends Controller
{
    public function index()
    {
        $userId = (int) auth()->id();

        // Semua split yang belum dibayar user (sebagai debtor)
        $splits = ExpenseSplit::with([
                'expense.event.eventMembers.user',
                'expense.payer',
            ])
            ->where('user_id', $userId)
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where('paid_by', '!=', $userId)
                  ->whereHas('event', fn($q2) => $q2->where('status', 'open'))
            )
            ->get();

        // Event cards — load lengkap untuk pt.event-card
        $eventIds = $splits->pluck('expense.event_id')->unique()->values();
        $events   = Event::whereIn('id', $eventIds)
            ->with(['eventMembers', 'expenses.splits'])
            ->get();

        // Tabel — tiap row = satu split, lengkap dengan EventMember untuk route
        $tableRows = $splits->map(function (ExpenseSplit $split) use ($userId) {
            $expense        = $split->expense;
            $event          = $expense->event;
            $debtorMember   = $event->eventMembers->firstWhere('user_id', $userId);
            $creditorMember = $expense->isGuestPayer()
                ? $event->eventMembers->firstWhere('guest_name', $expense->guest_payer_name)
                : $event->eventMembers->firstWhere('user_id', $expense->paid_by);

            return [
                'split'           => $split,
                'event'           => $event,
                'expense'         => $expense,
                'creditor_name'   => $expense->payerName(),
                'debtor_member'   => $debtorMember,
                'creditor_member' => $creditorMember,
                'amount'          => $split->remaining(),
            ];
        })->sortBy('event.name')->values();

        $totalOwe = $tableRows->sum('amount');

        return view('my-debts.index', compact('events', 'tableRows', 'totalOwe'));
    }
}
