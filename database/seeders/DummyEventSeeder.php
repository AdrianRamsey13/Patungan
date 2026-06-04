<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventMember;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyEventSeeder extends Seeder
{
    // User IDs sesuai DB:
    // 1 = Ramsey Adrian, 2 = Nathaniel Azario, 3 = Fahreza Adi, 4 = Falah Cesa

    public function run(): void
    {
        DB::transaction(function () {
            $this->liburanBali();
            $this->nobarFinalLiga();
            $this->kadoFalah();          // event selesai (closed)
            $this->sewaVillaPuncak();    // ada guest non-user
            $this->makanSiangWeekly();   // semua sudah lunas
            $this->arisanBulanan();
        });
    }

    // ── 1. Liburan ke Bali — aktif, 2 expense, bayar campuran ──
    private function liburanBali(): void
    {
        $event = Event::create([
            'name'        => 'Liburan ke Bali',
            'description' => 'Tiket pesawat + hotel 2 malam',
            'category'    => 'jalan',
            'created_by'  => 1,
            'date_start'  => '2026-06-12',
            'date_end'    => '2026-06-15',
            'status'      => 'open',
            'created_at'  => '2026-06-01 08:00:00',
            'updated_at'  => '2026-06-01 08:00:00',
        ]);

        $members = $this->addMembers($event->id, [1, 2, 3, 4], '2026-06-01 08:00:00');

        // Expense 1: Tiket pesawat — Ramsey nalangin, 1.200.000 / 4 = 300.000
        $exp1 = $this->addExpense($event->id, 1, null, 1200000, 'Tiket pesawat PP', '2026-06-01 08:05:00');
        $this->addSplits($exp1->id, [
            [1, null, 300000, 300000, 1, '2026-06-01 08:05:00'], // Ramsey (nalangin)
            [2, null, 300000, 0,      0, null],                   // Nathaniel — belum
            [3, null, 300000, 300000, 1, '2026-06-02 10:00:00'], // Fahreza — sudah
            [4, null, 300000, 0,      0, null],                   // Falah — belum
        ]);

        // Expense 2: Hotel — Nathaniel nalangin, 800.000 / 4 = 200.000
        $exp2 = $this->addExpense($event->id, 2, null, 800000, 'Hotel 2 malam', '2026-06-01 08:10:00');
        $this->addSplits($exp2->id, [
            [1, null, 200000, 0,      0, null],                   // Ramsey — belum
            [2, null, 200000, 200000, 1, '2026-06-01 08:10:00'], // Nathaniel (nalangin)
            [3, null, 200000, 0,      0, null],                   // Fahreza — belum
            [4, null, 200000, 0,      0, null],                   // Falah — belum
        ]);
    }

    // ── 2. Nobar Final Liga — aktif, 1 expense, sebagian bayar ──
    private function nobarFinalLiga(): void
    {
        $event = Event::create([
            'name'       => 'Nobar Final Liga',
            'category'   => 'acara',
            'created_by' => 4, // Falah creator
            'date_start' => '2026-06-07',
            'status'     => 'open',
            'created_at' => '2026-06-01 09:00:00',
            'updated_at' => '2026-06-01 09:00:00',
        ]);

        $this->addMembers($event->id, [4, 1, 2], '2026-06-01 09:00:00');

        // 420.000 / 3 = 140.000
        $exp = $this->addExpense($event->id, 4, null, 420000, 'Sewa proyektor + cemilan', '2026-06-01 09:05:00');
        $this->addSplits($exp->id, [
            [4, null, 140000, 140000, 1, '2026-06-01 09:05:00'], // Falah (nalangin)
            [1, null, 140000, 140000, 1, '2026-06-02 11:00:00'], // Ramsey — sudah
            [2, null, 140000, 0,      0, null],                   // Nathaniel — belum
        ]);
    }

    // ── 3. Kado Ulang Tahun Falah — SELESAI, semua lunas ────────
    private function kadoFalah(): void
    {
        $event = Event::create([
            'name'       => 'Kado Ulang Tahun Falah',
            'category'   => 'kado',
            'created_by' => 1,
            'date_start' => '2026-05-20',
            'status'     => 'closed',
            'created_at' => '2026-05-15 10:00:00',
            'updated_at' => '2026-05-21 12:00:00',
        ]);

        $this->addMembers($event->id, [1, 2, 3], '2026-05-15 10:00:00');

        // 600.000 / 3 = 200.000 — semua sudah bayar
        $exp = $this->addExpense($event->id, 1, null, 600000, 'Tas + buket bunga', '2026-05-15 10:05:00');
        $this->addSplits($exp->id, [
            [1, null, 200000, 200000, 1, '2026-05-15 10:05:00'],
            [2, null, 200000, 200000, 1, '2026-05-16 09:00:00'],
            [3, null, 200000, 200000, 1, '2026-05-17 14:00:00'],
        ]);
    }

    // ── 4. Sewa Villa Puncak — ada 2 guest non-user ──────────────
    private function sewaVillaPuncak(): void
    {
        $event = Event::create([
            'name'        => 'Sewa Villa Puncak',
            'description' => 'Villa 2 kamar, gathering tim',
            'category'    => 'sewa',
            'created_by'  => 1,
            'date_start'  => '2026-06-20',
            'date_end'    => '2026-06-22',
            'status'      => 'open',
            'created_at'  => '2026-06-03 07:00:00',
            'updated_at'  => '2026-06-03 07:00:00',
        ]);

        // 4 user + 2 guest = 6 peserta
        $this->addMembers($event->id, [1, 2, 3, 4], '2026-06-03 07:00:00');
        $this->addGuests($event->id, ['Budi Santoso', 'Sinta Wulandari'], '2026-06-03 07:00:00');

        // 2.400.000 / 6 = 400.000
        $exp = $this->addExpense($event->id, 1, null, 2400000, 'Villa 2 kamar 2 malam', '2026-06-03 07:05:00');
        $this->addSplits($exp->id, [
            [1,    null,              400000, 400000, 1, '2026-06-03 07:05:00'], // Ramsey (nalangin)
            [2,    null,              400000, 0,      0, null],                   // Nathaniel — belum
            [3,    null,              400000, 400000, 1, '2026-06-03 14:00:00'], // Fahreza — sudah
            [4,    null,              400000, 0,      0, null],                   // Falah — belum
            [null, 'Budi Santoso',    400000, 0,      0, null],                   // Budi (guest) — belum
            [null, 'Sinta Wulandari', 400000, 0,      0, null],                   // Sinta (guest) — belum
        ]);
    }

    // ── 5. Makan Siang Weekly — aktif, semua sudah lunas ─────────
    private function makanSiangWeekly(): void
    {
        $event = Event::create([
            'name'       => 'Makan Siang Weekly',
            'category'   => 'konsumsi',
            'created_by' => 2, // Nathaniel creator
            'date_start' => '2026-06-03',
            'status'     => 'open',
            'created_at' => '2026-06-03 11:30:00',
            'updated_at' => '2026-06-03 11:30:00',
        ]);

        $this->addMembers($event->id, [2, 1, 3, 4], '2026-06-03 11:30:00');

        // 240.000 / 4 = 60.000 — semua sudah bayar
        $exp = $this->addExpense($event->id, 2, null, 240000, 'Warteg langganan', '2026-06-03 11:35:00');
        $this->addSplits($exp->id, [
            [2, null, 60000, 60000, 1, '2026-06-03 11:35:00'],
            [1, null, 60000, 60000, 1, '2026-06-03 11:40:00'],
            [3, null, 60000, 60000, 1, '2026-06-03 12:00:00'],
            [4, null, 60000, 60000, 1, '2026-06-03 13:00:00'],
        ]);
    }

    // ── 6. Arisan Bulanan — aktif, sebagian bayar ─────────────────
    private function arisanBulanan(): void
    {
        $event = Event::create([
            'name'       => 'Arisan Bulanan',
            'category'   => 'acara',
            'created_by' => 3, // Fahreza creator
            'date_start' => '2026-06-01',
            'status'     => 'open',
            'created_at' => '2026-06-01 06:00:00',
            'updated_at' => '2026-06-01 06:00:00',
        ]);

        $this->addMembers($event->id, [3, 1, 4], '2026-06-01 06:00:00');

        // 450.000 / 3 = 150.000
        $exp = $this->addExpense($event->id, 3, null, 450000, 'Konsumsi arisan', '2026-06-01 06:05:00');
        $this->addSplits($exp->id, [
            [3, null, 150000, 150000, 1, '2026-06-01 06:05:00'], // Fahreza (nalangin)
            [1, null, 150000, 0,      0, null],                   // Ramsey — belum
            [4, null, 150000, 150000, 1, '2026-06-01 08:00:00'], // Falah — sudah
        ]);
    }

    // ── Helper methods ────────────────────────────────────────────

    private function addMembers(int $eventId, array $userIds, string $ts): array
    {
        $members = [];
        foreach ($userIds as $uid) {
            $members[] = EventMember::create([
                'event_id'   => $eventId,
                'user_id'    => $uid,
                'guest_name' => null,
                'joined_at'  => $ts,
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);
        }
        return $members;
    }

    private function addGuests(int $eventId, array $names, string $ts): void
    {
        foreach ($names as $name) {
            EventMember::create([
                'event_id'   => $eventId,
                'user_id'    => null,
                'guest_name' => $name,
                'joined_at'  => $ts,
                'created_at' => $ts,
                'updated_at' => $ts,
            ]);
        }
    }

    private function addExpense(int $eventId, ?int $paidBy, ?string $guestPayer, int $amount, string $desc, string $ts): Expense
    {
        return Expense::create([
            'event_id'          => $eventId,
            'paid_by'           => $paidBy,
            'guest_payer_name'  => $guestPayer,
            'amount'            => $amount,
            'description'       => $desc,
            'created_at'        => $ts,
            'updated_at'        => $ts,
        ]);
    }

    private function addSplits(int $expenseId, array $splits): void
    {
        foreach ($splits as [$userId, $guestName, $owed, $paid, $isPaid, $paidAt]) {
            ExpenseSplit::create([
                'expense_id'  => $expenseId,
                'user_id'     => $userId,
                'guest_name'  => $guestName,
                'amount_owed' => $owed,
                'amount_paid' => $paid,
                'is_paid'     => $isPaid,
                'paid_at'     => $paidAt,
            ]);
        }
    }
}
