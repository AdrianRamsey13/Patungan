<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSplit;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = (int) auth()->id();

        $history = ExpenseSplit::with([
                'expense.event',
                'expense.payer',
                'user',
            ])
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', '!=', $userId));
            })
            ->orWhere(function ($q) use ($userId) {
                $q->where(fn($q2) => $q2->where('user_id', '!=', $userId)->orWhereNull('user_id'))
                  ->where('is_paid', true)
                  ->whereHas('expense', fn($q2) => $q2->where('paid_by', $userId));
            })
            ->orderByDesc('paid_at')
            ->paginate(20);

        return view('history.index', compact('history'));
    }
}
