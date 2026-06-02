<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExpenseSplitService
{
    // ──────────────────────────────────────────────────────────
    // CREATE — buat splits untuk expense baru (semua member)
    // ──────────────────────────────────────────────────────────

    public function createSplits(Expense $expense, Collection $memberIds): void
    {
        $count = $memberIds->count();
        [$base, $remainder] = $this->divideAmount($expense->amount, $count);

        foreach ($memberIds->values() as $i => $userId) {
            $share    = $base + ($i === $count - 1 ? $remainder : 0);
            $isPayer  = $userId === $expense->paid_by;

            ExpenseSplit::create([
                'expense_id'  => $expense->id,
                'user_id'     => $userId,
                'amount_owed' => $share,
                'amount_paid' => $isPayer ? $share : 0,
                'is_paid'     => $isPayer,
                'paid_at'     => $isPayer ? now() : null,
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────
    // ADD MEMBER — recalc unpaid splits + buat split baru
    // Paid splits di-lock; sisa pool dibagi ulang
    // ──────────────────────────────────────────────────────────

    public function recalculateForAdd(Expense $expense, int $newMemberId): void
    {
        $splits     = $expense->splits;
        $totalPaid  = $splits->sum('amount_paid');
        $remaining  = $expense->amount - $totalPaid;
        $unpaid     = $splits->where('is_paid', false);
        $newCount   = $unpaid->count() + 1; // +1 untuk member baru

        if ($newCount === 0 || $remaining <= 0) {
            // Semua sudah lunas, buat split baru dengan 0
            ExpenseSplit::create([
                'expense_id'  => $expense->id,
                'user_id'     => $newMemberId,
                'amount_owed' => 0,
                'amount_paid' => 0,
                'is_paid'     => true,
            ]);
            return;
        }

        [$base, $rem] = $this->divideAmount($remaining, $newCount);

        // Update existing unpaid splits ke amount baru (base saja, remainder ke member baru)
        foreach ($unpaid as $split) {
            $split->update(['amount_owed' => $base]);
        }

        $isPayer = $newMemberId === $expense->paid_by;
        ExpenseSplit::create([
            'expense_id'  => $expense->id,
            'user_id'     => $newMemberId,
            'amount_owed' => $base + $rem,
            'amount_paid' => $isPayer ? ($base + $rem) : 0,
            'is_paid'     => $isPayer,
            'paid_at'     => $isPayer ? now() : null,
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // REMOVE MEMBER — recalc SEMUA splits (termasuk yang sudah bayar)
    // Selisih jadi sisa utang bagi yang sudah bayar sebagian
    // ──────────────────────────────────────────────────────────

    public function recalculateForRemove(Expense $expense, int $removedMemberId): void
    {
        $expense->splits()->where('user_id', $removedMemberId)->delete();
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
    // Return: Collection of settlement objects grouped by debtor→creditor
    // ──────────────────────────────────────────────────────────

    public function calculateSettlements(Event $event): Collection
    {
        $event->loadMissing([
            'expenses.splits.user',
            'expenses.payer',
        ]);

        $map = collect();

        foreach ($event->expenses as $expense) {
            foreach ($expense->splits as $split) {
                // Payer tidak utang ke diri sendiri
                if ($split->user_id === $expense->paid_by) continue;

                $remaining = $split->amount_owed - $split->amount_paid;

                $key = "{$split->user_id}_{$expense->paid_by}";

                if (! $map->has($key)) {
                    $map->put($key, [
                        'debtor'       => $split->user,
                        'creditor'     => $expense->payer,
                        'total_owed'   => 0,
                        'total_paid'   => 0,
                        'remaining'    => 0,
                        'splits'       => collect(),
                    ]);
                }

                $entry = $map->get($key);
                $entry['total_owed'] += $split->amount_owed;
                $entry['total_paid'] += $split->amount_paid;
                $entry['remaining']  += max(0, $remaining);
                $entry['splits']->push($split->load('expense'));
                $map->put($key, $entry);
            }
        }

        return $map->values();
    }

    // ──────────────────────────────────────────────────────────
    // DASHBOARD STATS — net balance user lintas semua event
    // ──────────────────────────────────────────────────────────

    public function calculateDashboardStats(User $user): array
    {
        // Total yang masih harus dibayar user (sebagai debtor)
        $owe = ExpenseSplit::where('user_id', $user->id)
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where('paid_by', '!=', $user->id)
                  ->whereHas('event', fn($q2) => $q2->where('status', 'open'))
            )
            ->selectRaw('SUM(amount_owed - amount_paid) as total, COUNT(DISTINCT expense_id) as cnt')
            ->first();

        // Hitung dari event yang user nalangin
        $receive = ExpenseSplit::where('user_id', '!=', $user->id)
            ->where('is_paid', false)
            ->whereHas('expense', fn($q) =>
                $q->where('paid_by', $user->id)
                  ->whereHas('event', fn($q2) => $q2->where('status', 'open'))
            )
            ->selectRaw('SUM(amount_owed - amount_paid) as total, COUNT(DISTINCT user_id) as people')
            ->first();

        // Event yang saya ikut (open)
        $activeEvents = $user->events()->where('events.status', 'open')->count();

        return [
            'total_owe'      => (int) ($owe->total ?? 0),
            'owe_count'      => (int) ($owe->cnt ?? 0),
            'total_receive'  => (int) ($receive->total ?? 0),
            'receive_people' => (int) ($receive->people ?? 0),
            'active_events'  => $activeEvents,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // MARK ALL PAID — debtor bayar ke satu creditor sekaligus
    // ──────────────────────────────────────────────────────────

    public function markAllPaid(Event $event, int $debtorId, int $creditorId): void
    {
        DB::transaction(function () use ($event, $debtorId, $creditorId) {
            ExpenseSplit::whereHas('expense', fn($q) =>
                    $q->where('event_id', $event->id)
                      ->where('paid_by', $creditorId)
                )
                ->where('user_id', $debtorId)
                ->where('is_paid', false)
                ->each(function (ExpenseSplit $split) {
                    $split->update([
                        'amount_paid' => $split->amount_owed,
                        'is_paid'     => true,
                        'paid_at'     => now(),
                    ]);
                });
        });
    }

    // ──────────────────────────────────────────────────────────
    // CAN REMOVE — cek apakah member bisa dikeluarkan
    // ──────────────────────────────────────────────────────────

    public function canRemoveMember(Event $event, int $userId): array
    {
        $hasPaid = ExpenseSplit::where('user_id', $userId)
            ->where('is_paid', true)
            ->whereHas('expense', fn($q) => $q->where('event_id', $event->id))
            ->exists();

        if ($hasPaid) {
            return [false, 'Member sudah melakukan pembayaran dan tidak bisa dikeluarkan.'];
        }

        $hasExpense = $event->expenses()->where('paid_by', $userId)->exists();

        if ($hasExpense) {
            return [false, 'Member punya pengeluaran aktif. Hapus pengeluarannya terlebih dahulu.'];
        }

        return [true, null];
    }

    // ──────────────────────────────────────────────────────────
    // HELPER
    // ──────────────────────────────────────────────────────────

    private function divideAmount(int $total, int $count): array
    {
        if ($count <= 0) return [0, 0];
        $base      = intdiv($total, $count);
        $remainder = $total % $count;
        return [$base, $remainder];
    }
}
