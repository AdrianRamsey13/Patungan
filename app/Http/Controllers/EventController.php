<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Services\ExpenseSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function __construct(private ExpenseSplitService $splits) {}

    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        // Tampilkan semua user lain (bukan hanya teman) supaya bisa langsung dipakai
        $users = User::where('id', '!=', auth()->id())->orderBy('name')->get();
        return view('events.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:jalan,konsumsi,acara,sewa,kado'],
            'date_start'  => ['nullable', 'date'],
            'date_end'    => ['nullable', 'date', 'after_or_equal:date_start'],
            'description' => ['nullable', 'string', 'max:1000'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['integer', 'exists:users,id'],
        ]);

        $event = DB::transaction(function () use ($data) {
            $user  = auth()->user();
            $event = Event::create([
                'name'        => $data['name'],
                'category'    => $data['category'],
                'date_start'  => $data['date_start'] ?? null,
                'date_end'    => $data['date_end'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by'  => $user->id,
                'status'      => 'open',
            ]);

            // Creator otomatis member + teman yang dipilih
            $memberIds = collect($data['members'] ?? [])->push($user->id)->unique();
            $event->members()->attach(
                $memberIds->all(),
                ['joined_at' => now()]
            );

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
            'members',
            'expenses.splits.user',
            'expenses.payer',
        ]);

        $userId      = (int) auth()->id();
        $settlements = $this->splits->calculateSettlements($event);
        $myDebts     = $settlements->filter(fn($s) => $s['debtor']->id === $userId);
        $myCredits   = $settlements->filter(fn($s) => $s['creditor']->id === $userId);

        $paidCount   = $event->members->filter(function ($member) use ($event) {
            // Member dianggap lunas jika semua splitnya lunas (atau dia sendiri yang nalangin)
            $splits = $event->expenses->flatMap->splits->where('user_id', $member->id);
            $debts  = $splits->filter(fn($s) => $s->expense->paid_by !== $member->id);
            return $debts->isEmpty() || $debts->every(fn($s) => $s->is_paid);
        })->count();

        // User yang belum ada di event ini (untuk chip picker tambah anggota)
        $memberIds      = $event->members->pluck('id');
        $availableUsers = User::whereNotIn('id', $memberIds)->orderBy('name')->get();

        return view('events.show', compact(
            'event', 'userId', 'settlements',
            'myDebts', 'myCredits', 'paidCount',
            'availableUsers'
        ));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        $event->load('members');
        $memberIds = $event->members->pluck('id');
        $addable   = User::whereNotIn('id', $memberIds)->orderBy('name')->get();

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

    // ── Tambah member ke event ──────────────────────────────

    public function addMember(Request $request, Event $event)
    {
        $this->authorize('update', $event);
        abort_if($event->status === 'closed', 422, 'Event sudah ditutup.');

        $data = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $existingIds = $event->members()->pluck('users.id')->map(fn($id) => (int) $id)->toArray();
        $newIds      = array_values(array_diff(
            array_map('intval', $data['user_ids']),
            $existingIds
        ));

        if (empty($newIds)) {
            return back()->with('error', 'Semua user yang dipilih sudah ada di event ini.');
        }

        DB::transaction(function () use ($event, $newIds) {
            foreach ($newIds as $userId) {
                $event->members()->attach($userId, ['joined_at' => now()]);
                foreach ($event->expenses()->with('splits')->get() as $expense) {
                    $this->splits->recalculateForAdd($expense, $userId);
                }
            }
        });

        $count = count($newIds);
        return back()->with('success', "{$count} anggota berhasil ditambahkan.");
    }

    // ── Keluarkan member dari event ─────────────────────────

    public function removeMember(Request $request, Event $event, User $user)
    {
        $this->authorize('update', $event);
        abort_if($event->created_by === $user->id, 422, 'Creator event tidak bisa dikeluarkan.');

        [$canRemove, $reason] = $this->splits->canRemoveMember($event, $user->id);

        if (! $canRemove) {
            return back()->with('error', $reason);
        }

        DB::transaction(function () use ($event, $user) {
            foreach ($event->expenses()->with('splits')->get() as $expense) {
                $this->splits->recalculateForRemove($expense, $user->id);
            }
            $event->members()->detach($user->id);
        });

        return back()->with('success', "{$user->name} berhasil dikeluarkan dari event.");
    }
}
