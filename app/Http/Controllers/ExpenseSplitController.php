<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMember;
use App\Services\ExpenseSplitService;

class ExpenseSplitController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    /**
     * Tandai lunas: debtor (EventMember) bayar ke creditor (EventMember).
     *
     * Aturan:
     * - Registered user bisa tandai diri sendiri (debtor_member.user_id = auth)
     * - Guest splits hanya bisa ditandai oleh creator event
     * - Splits dengan creditor guest hanya bisa ditandai creator
     */
    public function markPaid(Event $event, EventMember $debtorMember, EventMember $creditorMember)
    {
        $this->authorize('view', $event);

        $userId    = (int) auth()->id();
        $isCreator = $event->created_by === $userId;

        // Validasi: hanya creator yang bisa tandai lunas untuk/dari guest
        if ($debtorMember->isGuest() || $creditorMember->isGuest()) {
            abort_if(! $isCreator, 403, 'Hanya creator yang bisa mengkonfirmasi pembayaran tamu.');
        } else {
            // Registered user: hanya bisa tandai utang diri sendiri
            abort_if($debtorMember->user_id !== $userId && ! $isCreator, 403);
        }

        abort_if($debtorMember->id === $creditorMember->id, 422, 'Tidak bisa bayar ke diri sendiri.');

        $this->splits->markAllPaid($event, $debtorMember, $creditorMember);

        $debtorName   = $debtorMember->isGuest() ? $debtorMember->guest_name : 'Kamu';
        $creditorName = $creditorMember->displayName();

        return back()->with('success', "Pembayaran {$debtorName} ke {$creditorName} dikonfirmasi!");
    }
}
