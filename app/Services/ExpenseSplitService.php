<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventMember;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExpenseSplitService
{
    // ──────────────────────────────────────────────────────────
    // CREATE — buat splits untuk expense baru
    // $members: Collection of EventMember (user atau guest)
    // ──────────────────────────────────────────────────────────

    public function createSplits(Expense $expense, Collection $members): void
    {
        $count = $members->count();
        if ($count === 0) return;

        [$base, $remainder] = $this->divideAmount($expense->amount, $count);

        foreach ($members->values() as $i => $member) {
            $share     = $base + ($i === $count - 1 ? $remainder : 0);
            $isPayer   = $expense->isPayerMember($member);

            ExpenseSplit::create([
                'expense_id'  => $expense->id,
                'user_id'     => $member->isGuest() ? null : $member->user_id,
                'guest_name'  => $member->isGuest() ? $member->guest_name : null,
                'amount_owed' => $share,
                'amount_paid' => $isPayer ? $share : 0,
                'is_paid'     => $isPayer,
                'paid_at'     => $isPayer ? now() : null,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────
    // ADD MEMBER — recalc unpaid splits + buat split baru
    // ──────────────────────────────────────────────────────────

    public function recalculateForAdd(Expense $expense, EventMember $newMember): void
    {
        $splits    = $expense->splits;
        $totalPaid = $splits->sum('amount_paid');
        $remaining = $expense->amount - $totalPaid;
        $unpaid    = $splits->where('is_paid', false);
        $newCount  = $unpaid->count() + 1;

        if ($remaining <= 0) {
            ExpenseSplit::create([
                'expense_id'  => $expense->id,
                'user_id'     => $newMember->isGuest() ? null : $newMember->user_id,
                'guest_name'  => $newMember->isGuest() ? $newMember->guest_name : null,
                'amount_owed' => 0,
                'amount_paid' => 0,
                'is_paid'     => true,
            ]);
            return;
        }

        [$base, $rem] = $this->divideAmount($remaining, $newCount);

        foreach ($unpaid as $split) {
            $split->update(['amount_owed' => $base]);
        }

        $isPayer = $expense->isPayerMember($newMember);
        ExpenseSplit::create([
            'expense_id'  => $expense->id,
            'user_id'     => $newMember->isGuest() ? null : $newMember->user_id,
            'guest_name'  => $newMember->isGuest() ? $newMember->guest_name : null,
            'amount_owed' => $base + $rem,
            'amount_paid' => $isPayer ? ($base + $rem) : 0,
            'is_paid'     => $isPayer,
            'paid_at'     => $isPayer ? now() : null,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // REMOVE MEMBER — recalc SEMUA splits
    // ──────────────────────────────────────────────────────────

    public function recalculateForRemove(Expense $expense, EventMember $member): void
    {
        // Hapus split milik member ini
        if ($member->isGuest()) {
            $expense->splits()->where('guest_name', $member->guest_name)->delete();
        } else {
            $expense->splits()->where('user_id', $member->user_id)->delete();
        }

        $expense->unsetRelation('splits');
        $remaining = $expense->splits()->get();
        $count     = $remaining->count();
        if ($count === 0) return;

        [$base, $rem] = $this->divideAmount($expense->amount, $count);

        foreach ($remaining->values() as $i => $split) {
            $isLast    = ($i === $count - 1);
            $newAmount = $base + ($isLast ? $rem : 0);
            $split->update([
                'amount_owed' => $newAmount,
                'is_paid'     => $split->amount_paid >= $newAmount,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────
    // SETTLEMENTS — siapa utang berapa ke siapa dalam 1 event
    // Return: Collection keyed by "debtorMemberId_creditorMemberId"
    // ──────────────────────────────────────────────────────────

    public function calculateSettlements(Event $event): Collection
    {
        $event->loadMissing([
            'eventMembers.user',
            'expenses.splits.user',
            'expenses.payer',
        ]);

        // Index EventMember by (user_id or guest_name)
        $memberByUserId    = $event->eventMembers->whereNotNull('user_id')->keyBy('user_id');
        $memberByGuestName = $event->eventMembers->whereNull('user_id')->keyBy('guest_name');

        $map = collect();

        foreach ($event->expenses as $expense) {
            // Find creditor EventMember
            $creditorMember = $expense->isGuestPayer()
                ? ($memberByGuestName->get($expense->guest_payer_name))
                : ($memberByUserId->get($expense->paid_by));

            if (! $creditorMember) continue;

            foreach ($expense->splits as $split) {
                // Find debtor EventMember
                $debtorMember = $split->isGuestSplit()
                    ? ($memberByGuestName->get($split->guest_name))
                    : ($memberByUserId->get($split->user_id));

                if (! $debtorMember) continue;

                // Payer tidak utang ke diri sendiri
                if ($debtorMember->id === $creditorMember->id) continue;

                $key = "{$debtorMember->id}_{$creditorMember->id}";

                if (! $map->has($key)) {
                    $map->put($key, [
                        'debtor_member'   => $debtorMember,
                        'creditor_member' => $creditorMember,
                        'total_owed'      => 0,
                        'total_paid'      => 0,
                        'remaining'       => 0,
                        'splits'          => collect(),
                    ]);
                }

                $entry = $map->get($key);
                $entry['total_owed'] += $split->amount_owed;
                $entry['total_paid'] += $split->amount_paid;
                $entry['remaining']  += max(0, $split->amount_owed - $split->amount_paid);
                $entry['splits']->push($split->load('expense'));
                $map->put($key, $entry);
            }
        }

        return $map->values();
    }

    // ──────────────────────────────────────────────────────────
    // DASHBOARD STATS
    // ──────────────────────────────────────────────────────────

    public function calculateDashboardStats(User $user): array
    {
        $owe = ExpenseSplit::where('user_id', $user->id)
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where(fn($q2) => $q2->where('paid_by', '!=', $user->id)->orWhereNull('paid_by'))
                  ->whereHas('event', fn($q3) => $q3->where('status', 'open'))
            )
            ->selectRaw('SUM(amount_owed - amount_paid) as total, COUNT(DISTINCT expense_id) as cnt')
            ->first();

        // Splits yang orang lain (termasuk guest) belum bayar ke user ini sebagai payer
        $receive = ExpenseSplit::where(function ($q) use ($user) {
                $q->where('user_id', '!=', $user->id)->orWhereNull('user_id');
            })
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where('paid_by', $user->id)
                  ->whereHas('event', fn($q2) => $q2->where('status', 'open'))
            )
            ->selectRaw('SUM(amount_owed - amount_paid) as total, COUNT(DISTINCT COALESCE(user_id, -1)) as people')
            ->first();

        $activeEvents = EventMember::where('user_id', $user->id)
            ->whereHas('event', fn($q) => $q->where('status', 'open'))
            ->count();

        return [
            'total_owe'      => (int) ($owe->total ?? 0),
            'owe_count'      => (int) ($owe->cnt ?? 0),
            'total_receive'  => (int) ($receive->total ?? 0),
            'receive_people' => (int) ($receive->people ?? 0),
            'active_events'  => $activeEvents,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // MARK ALL PAID — debtor EventMember bayar ke creditor EventMember
    // ──────────────────────────────────────────────────────────

    public function markAllPaid(Event $event, EventMember $debtor, EventMember $creditor): void
    {
        DB::transaction(function () use ($event, $debtor, $creditor) {
            $query = ExpenseSplit::whereHas('expense', fn($q) =>
                $q->where('event_id', $event->id)
                  ->when($creditor->isGuest(),
                      fn($q2) => $q2->where('guest_payer_name', $creditor->guest_name)->whereNull('paid_by'),
                      fn($q2) => $q2->where('paid_by', $creditor->user_id)
                  )
            );

            if ($debtor->isGuest()) {
                $query->where('guest_name', $debtor->guest_name)->whereNull('user_id');
            } else {
                $query->where('user_id', $debtor->user_id);
            }

            $query->where('is_paid', false)->each(fn(ExpenseSplit $s) => $s->markAsPaid());
        });
    }

    // ──────────────────────────────────────────────────────────
    // CAN REMOVE
    // ──────────────────────────────────────────────────────────

    public function canRemoveMember(Event $event, EventMember $member): array
    {
        $splitsQuery = ExpenseSplit::whereHas('expense', fn($q) => $q->where('event_id', $event->id));

        if ($member->isGuest()) {
            $splitsQuery->where('guest_name', $member->guest_name)->whereNull('user_id');
        } else {
            $splitsQuery->where('user_id', $member->user_id);
        }

        if ($splitsQuery->clone()->where('is_paid', true)->exists()) {
            return [false, 'Member sudah melakukan pembayaran dan tidak bisa dikeluarkan.'];
        }

        // Cek apakah pernah nalangin (jadi creditor)
        $expenseQuery = $event->expenses();
        if ($member->isGuest()) {
            $expenseQuery->where('guest_payer_name', $member->guest_name)->whereNull('paid_by');
        } else {
            $expenseQuery->where('paid_by', $member->user_id);
        }

        if ($expenseQuery->exists()) {
            return [false, 'Member punya pengeluaran aktif. Hapus pengeluarannya terlebih dahulu.'];
        }

        return [true, null];
    }

    // ──────────────────────────────────────────────────────────
    // MARK ALL GUESTS PAID — creator tandai semua guest lunas sekaligus
    // ──────────────────────────────────────────────────────────

    public function markAllGuestsPaid(Event $event): int
    {
        $count = 0;
        DB::transaction(function () use ($event, &$count) {
            $splits = ExpenseSplit::whereNull('user_id')
                ->whereNotNull('guest_name')
                ->where('is_paid', false)
                ->whereHas('expense', fn($q) => $q->where('event_id', $event->id))
                ->get();

            foreach ($splits as $split) {
                $split->markAsPaid();
                $count++;
            }
        });
        return $count;
    }

    // ──────────────────────────────────────────────────────────
    // HELPER
    // ──────────────────────────────────────────────────────────

    private function divideAmount(int $total, int $count): array
    {
        if ($count <= 0) return [0, 0];
        return [intdiv($total, $count), $total % $count];
    }
}
