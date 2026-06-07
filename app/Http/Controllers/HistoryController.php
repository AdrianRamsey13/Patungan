<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSplit;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = (int) auth()->id();

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [5, 10, 20])) {
            $perPage = 10;
        }

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
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('history.index', compact('history', 'perPage'));
    }
}
