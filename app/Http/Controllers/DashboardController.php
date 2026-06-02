<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSplit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tab  = $request->query('tab', 'aktif');

        // Events yang diikuti user, eager load semua relasi yang dibutuhkan
        $eventsQuery = $user->events()
            ->with(['creator', 'members', 'expenses.splits'])
            ->withCount('members');

        $events = match ($tab) {
            'selesai' => (clone $eventsQuery)->where('events.status', 'closed')->get(),
            'semua'   => $eventsQuery->get(),
            default   => (clone $eventsQuery)->where('events.status', 'open')->get(),
        };

        // Semua split aktif yang melibatkan user ini
        $mySplits = ExpenseSplit::with(['expense.event', 'expense.payer', 'user'])
            ->where('user_id', $user->id)
            ->whereHas('expense.event', fn($q) => $q->where('status', 'open'))
            ->get();

        // Split di event yang user nalangin (uang yang bakal diterima)
        $receiveSplits = ExpenseSplit::with(['expense.event', 'user'])
            ->whereHas('expense', fn($q) => $q->where('paid_by', $user->id))
            ->where('user_id', '!=', $user->id)
            ->where('is_paid', false)
            ->whereHas('expense.event', fn($q) => $q->where('status', 'open'))
            ->get();

        $totalOwe      = $mySplits->where('is_paid', false)
            ->filter(fn($s) => $s->expense->paid_by !== $user->id)
            ->sum('amount_owed');

        $totalReceive  = $receiveSplits->sum('amount_owed');
        $oweCount      = $user->events()->where('events.status', 'open')
            ->get()->filter(fn($e) => $e->expenses->flatMap->splits
                ->where('user_id', $user->id)
                ->where('is_paid', false)
                ->first()?->expense?->paid_by !== $user->id
            )->count();
        $receivePeople = $receiveSplits->pluck('user_id')->unique()->count();

        // Riwayat — split yang sudah dibayar (masuk/keluar)
        $history = ExpenseSplit::with(['expense.event', 'expense.payer', 'user'])
            ->where(function ($q) use ($user) {
                // Split yang saya bayar (uang keluar)
                $q->where('user_id', $user->id)->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', '!=', $user->id));
            })
            ->orWhere(function ($q) use ($user) {
                // Split yang orang lain bayar ke saya (uang masuk)
                $q->where('user_id', '!=', $user->id)->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', $user->id));
            })
            ->orderByDesc('paid_at')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'events', 'tab',
            'totalOwe', 'totalReceive', 'oweCount', 'receivePeople',
            'history'
        ));
    }
}
