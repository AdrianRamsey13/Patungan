<?php

namespace App\Http\Controllers;

use App\Models\ExpenseSplit;
use Illuminate\Http\Request;

class ExpenseSplitController extends Controller
{
    // Tandai lunas — hanya bisa dilakukan oleh user yang bersangkutan
    public function markPaid(Request $request, ExpenseSplit $expenseSplit)
    {
        abort_if($expenseSplit->user_id !== $request->user()->id, 403);
        abort_if($expenseSplit->is_paid, 422, 'Sudah lunas.');

        $expenseSplit->markAsPaid();

        return back()->with('success', 'Pembayaran dikonfirmasi!');
    }
}
