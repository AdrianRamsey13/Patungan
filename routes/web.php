<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseSplitController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteEntryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('landing');
})->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Events (resource kecuali index)
    Route::resource('events', EventController::class)->except(['index']);

    // Member management (pakai EventMember ID supaya cover user + guest)
    Route::post('/events/{event}/members',                              [EventController::class, 'addMember'])->name('events.members.add');
    Route::delete('/events/{event}/members/{eventMember}',             [EventController::class, 'removeMember'])->name('events.members.remove');
    Route::post('/events/{event}/mark-all-guests-paid',                [EventController::class, 'markAllGuestsPaid'])->name('events.guests.mark-all-paid');

    // Expenses — nested di bawah event
    Route::get('/events/{event}/expenses/create',                      [ExpenseController::class, 'create'])->name('events.expenses.create');
    Route::post('/events/{event}/expenses',                            [ExpenseController::class, 'store'])->name('events.expenses.store');

    // Tandai Lunas — route pakai EventMember ID (cover user + guest)
    Route::post('/events/{event}/pay/{debtorMember}/{creditorMember}', [ExpenseSplitController::class, 'markPaid'])->name('events.pay');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notes — personal debt/piutang tracker
    Route::get('/notes',                                      [NoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/create',                               [NoteController::class, 'create'])->name('notes.create');
    Route::post('/notes',                                     [NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{note}',                               [NoteController::class, 'show'])->name('notes.show');
    Route::delete('/notes/{note}',                            [NoteController::class, 'destroy'])->name('notes.destroy');
    Route::get('/notes/{note}/entries/{entryType}',           [NoteEntryController::class, 'create'])->name('notes.entries.create');
    Route::post('/notes/{note}/entries',                      [NoteEntryController::class, 'store'])->name('notes.entries.store');
    Route::delete('/notes/{note}/entries/{entry}',            [NoteEntryController::class, 'destroy'])->name('notes.entries.destroy');

    // Stub pages
    Route::get('/friends', fn() => view('friends.index'))->name('friends.index');
    Route::get('/bills',   fn() => view('bills.index'))->name('bills.index');
});

require __DIR__.'/auth.php';
