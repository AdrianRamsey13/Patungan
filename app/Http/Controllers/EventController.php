<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMember;
use App\Models\User;
use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    public function index() { return redirect()->route('dashboard'); }

    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get();
        return view('events.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'category'          => ['required', 'in:jalan,konsumsi,acara,sewa,kado'],
            'date_start'        => ['nullable', 'date'],
            'date_end'          => ['nullable', 'date', 'after_or_equal:date_start'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'members'           => ['nullable', 'array'],
            'members.*'         => ['integer', 'exists:users,id'],
            'guests'            => ['nullable', 'array', 'max:30'],
            'guests.*.name'     => ['required_with:guests', 'string', 'max:100'],
        ]);

        // Validasi total cap 50 orang
        $registeredCount = count($data['members'] ?? []) + 1;
        $guestCount      = count($data['guests'] ?? []);

        if ($registeredCount + $guestCount > 50) {
            return back()->withErrors(['members' => 'Total peserta tidak boleh lebih dari 50 orang.'])->withInput();
        }

        $event = DB::transaction(function () use ($data) {
            $event = Event::create([
                'name'        => $data['name'],
                'category'    => $data['category'],
                'date_start'  => $data['date_start'] ?? null,
                'date_end'    => $data['date_end'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by'  => auth()->id(),
                'status'      => 'open',
            ]);

            // Creator
            EventMember::create(['event_id' => $event->id, 'user_id' => auth()->id(), 'joined_at' => now()]);

            // Registered members
            foreach ($data['members'] ?? [] as $userId) {
                if ((int)$userId !== auth()->id()) {
                    EventMember::create(['event_id' => $event->id, 'user_id' => $userId, 'joined_at' => now()]);
                }
            }

            // Guests
            foreach ($data['guests'] ?? [] as $guest) {
                EventMember::create([
                    'event_id'   => $event->id,
                    'user_id'    => null,
                    'guest_name' => trim($guest['name']),
                    'joined_at'  => now(),
                ]);
            }

            return $event;
        });

        return redirect()->route('events.show', $event)
            ->with('success', 'Event berhasil dibuat! Sekarang tambahkan pengeluaran.');
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);

        $event->load([
            'creator',
            'eventMembers.user',
            'expenses.splits.user',
            'expenses.payer',
        ]);

        $userId      = (int) auth()->id();
        $settlements = $this->splits->calculateSettlements($event);

        // Split berdasarkan debtor: apakah debtor adalah auth user
        $myDebts   = $settlements->filter(fn($s) =>
            ! $s['debtor_member']->isGuest() && $s['debtor_member']->user_id === $userId
        );
        $myCredits = $settlements->filter(fn($s) =>
            ! $s['creditor_member']->isGuest() && $s['creditor_member']->user_id === $userId
        );

        // Settlements yang melibatkan guest (hanya untuk creator)
        $guestSettlements = collect();
        if ($userId === $event->created_by) {
            $guestSettlements = $settlements->filter(fn($s) =>
                $s['debtor_member']->isGuest() || $s['creditor_member']->isGuest()
            )->filter(fn($s) => $s['remaining'] > 0);
        }

        $paidCount   = $this->countFullyPaidMembers($event, $settlements);
        $totalMembers = $event->eventMembers->count();

        $memberIds      = $event->eventMembers->pluck('id');
        $availableUsers = User::whereNotIn('id',
            $event->eventMembers->whereNotNull('user_id')->pluck('user_id')
        )->orderBy('name')->get();

        return view('events.show', compact(
            'event', 'userId', 'settlements',
            'myDebts', 'myCredits', 'guestSettlements',
            'paidCount', 'totalMembers', 'availableUsers'
        ));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $event->load('eventMembers.user');
        $memberUserIds = $event->eventMembers->whereNotNull('user_id')->pluck('user_id');
        $addable = User::whereNotIn('id', $memberUserIds)->orderBy('name')->get();

        return view('events.edit', compact('event', 'addable'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:jalan,konsumsi,acara,sewa,kado'],
            'date_start'  => ['nullable', 'date'],
            'date_end'    => ['nullable', 'date', 'after_or_equal:date_start'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', 'in:open,closed'],
        ]);
        $event->update($data);
        return redirect()->route('events.show', $event)->with('success', 'Event diperbarui.');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();
        return redirect()->route('dashboard')->with('success', 'Event dihapus.');
    }

    // ── Tambah user terdaftar ke event ──────────────────────

    public function addMember(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        abort_if($event->status === 'closed', 422, 'Event sudah ditutup.');

        $data = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        // Cek total cap
        $currentCount = $event->eventMembers()->count();
        if ($currentCount + count($data['user_ids']) > 50) {
            return back()->with('error', 'Total peserta tidak boleh lebih dari 50 orang.');
        }

        $existingUserIds = $event->eventMembers->whereNotNull('user_id')->pluck('user_id')->map(fn($id) => (int)$id)->toArray();
        $newIds = array_values(array_diff(array_map('intval', $data['user_ids']), $existingUserIds));

        if (empty($newIds)) {
            return back()->with('error', 'Semua user yang dipilih sudah ada di event ini.');
        }

        DB::transaction(function () use ($event, $newIds) {
            foreach ($newIds as $userId) {
                $member = EventMember::create(['event_id' => $event->id, 'user_id' => $userId, 'joined_at' => now()]);
                foreach ($event->expenses()->with('splits')->get() as $expense) {
                    $this->splits->recalculateForAdd($expense, $member);
                }
            }
        });

        return back()->with('success', count($newIds) . ' anggota berhasil ditambahkan.');
    }

    // ── Keluarkan member (user atau guest) dari event ───────

    public function removeMember(Event $event, EventMember $eventMember)
    {
        $this->authorize('update', $event);
        abort_if($event->created_by === $eventMember->user_id && ! $eventMember->isGuest(), 422, 'Creator tidak bisa dikeluarkan.');

        [$canRemove, $reason] = $this->splits->canRemoveMember($event, $eventMember);
        if (! $canRemove) {
            return back()->with('error', $reason);
        }

        DB::transaction(function () use ($event, $eventMember) {
            foreach ($event->expenses()->with('splits')->get() as $expense) {
                $this->splits->recalculateForRemove($expense, $eventMember);
            }
            $eventMember->delete();
        });

        $name = $eventMember->displayName();
        return back()->with('success', "{$name} berhasil dikeluarkan.");
    }

    // ── Creator tandai semua guest lunas sekaligus ──────────

    public function markAllGuestsPaid(Event $event)
    {
        $this->authorize('update', $event);
        $count = $this->splits->markAllGuestsPaid($event);
        return back()->with('success', "{$count} pembayaran tamu berhasil dikonfirmasi.");
    }

    // ── Helper ──────────────────────────────────────────────

    private function countFullyPaidMembers(Event $event, Collection $settlements): int
    {
        return $event->eventMembers->filter(function (EventMember $member) use ($settlements) {
            $debts = $settlements->filter(fn($s) => $s['debtor_member']->id === $member->id);
            return $debts->isEmpty() || $debts->every(fn($s) => $s['remaining'] === 0);
        })->count();
    }
}
