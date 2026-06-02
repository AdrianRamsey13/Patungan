<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;

class ExpenseSplitController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    // Debtor tandai lunas ke satu creditor spesifik dalam event
    public function markPaid(Request $request, Event $event, User $creditor)
    {
        $this->authorize('view', $event);

        $debtorId = (int) auth()->id();

        abort_if($debtorId === $creditor->id, 422, 'Tidak bisa bayar ke diri sendiri.');

        $this->splits->markAllPaid($event, $debtorId, $creditor->id);

        return back()->with('success', "Pembayaran ke {$creditor->name} dikonfirmasi!");
    }
}
