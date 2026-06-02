<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        $friends = auth()->user()->friends()->get();
        return view('events.create', compact('friends'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:jalan,konsumsi,acara,sewa,kado'],
            'date_start'  => ['nullable', 'date'],
            'date_end'    => ['nullable', 'date', 'after_or_equal:date_start'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount'      => ['required', 'integer', 'min:1'],
            'members'     => ['nullable', 'array'],
            'members.*'   => ['integer', 'exists:users,id'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $user = $request->user();

            $event = Event::create([
                'name'        => $data['name'],
                'category'    => $data['category'],
                'date_start'  => $data['date_start'] ?? null,
                'date_end'    => $data['date_end'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by'  => $user->id,
                'status'      => 'open',
            ]);

            // Creator otomatis jadi member
            $memberIds = collect($data['members'] ?? [])->push($user->id)->unique();
            $event->members()->attach($memberIds->all(), ['joined_at' => now()]);

            // Buat expense tunggal dengan split rata
            $expense = $event->expenses()->create([
                'paid_by'     => $user->id,
                'amount'      => $data['amount'],
                'description' => $data['name'],
            ]);

            $expense->createEvenSplits();

            $this->redirectTarget = $event;
        });

        return redirect()->route('events.show', $this->redirectTarget)
            ->with('success', 'Event berhasil dibuat!');
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

        $userId = auth()->id();
        $iAmPayer = $event->created_by === $userId;

        // Split milik user login di event ini
        $mySplit = $event->expenses->flatMap->splits->firstWhere('user_id', $userId);

        // Jumlah yang bakal diterima (jika nalangin)
        $toReceive = $iAmPayer
            ? $event->expenses->flatMap->splits
                ->where('user_id', '!=', $userId)
                ->where('is_paid', false)
                ->sum('amount_owed')
            : 0;

        // Jumlah yang harus dibayar (jika utang)
        $iOwe = (!$iAmPayer && $mySplit && !$mySplit->is_paid)
            ? $mySplit->amount_owed
            : 0;

        $paidCount   = $event->expenses->flatMap->splits->where('is_paid', true)->pluck('user_id')->unique()->count();
        $memberCount = $event->members->count();

        return view('events.show', compact(
            'event', 'userId', 'iAmPayer', 'mySplit',
            'toReceive', 'iOwe', 'paidCount', 'memberCount'
        ));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
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

    private Event $redirectTarget;
}
