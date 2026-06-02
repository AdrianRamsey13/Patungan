<?php

namespace App\Http\Controllers;

use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $tab  = $request->query('tab', 'aktif');

        $eventsQuery = $user->events()
            ->with(['creator', 'members', 'expenses.splits'])
            ->withCount('members');

        $events = match ($tab) {
            'selesai' => (clone $eventsQuery)->where('events.status', 'closed')->get(),
            'semua'   => $eventsQuery->get(),
            default   => (clone $eventsQuery)->where('events.status', 'open')->get(),
        };

        $stats    = $this->splits->calculateDashboardStats($user);
        $history  = $this->buildHistory($user->id);

        return view('dashboard', [
            'events'         => $events,
            'tab'            => $tab,
            'totalOwe'       => $stats['total_owe'],
            'totalReceive'   => $stats['total_receive'],
            'oweCount'       => $stats['owe_count'],
            'receivePeople'  => $stats['receive_people'],
            'history'        => $history,
        ]);
    }

    private function buildHistory(int $userId)
    {
        return \App\Models\ExpenseSplit::with([
                'expense.event',
                'expense.payer',
                'user',
            ])
            ->where(function ($q) use ($userId) {
                // Split yang saya bayar (uang keluar)
                $q->where('user_id', $userId)
                  ->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', '!=', $userId));
            })
            ->orWhere(function ($q) use ($userId) {
                // Split yang orang lain bayar ke saya (uang masuk)
                $q->where('user_id', '!=', $userId)
                  ->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', $userId));
            })
            ->orderByDesc('paid_at')
            ->limit(6)
            ->get();
    }
}
