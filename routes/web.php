<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseSplitController;
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

    // Member management
    Route::post('/events/{event}/members',              [EventController::class, 'addMember'])->name('events.members.add');
    Route::delete('/events/{event}/members/{user}',     [EventController::class, 'removeMember'])->name('events.members.remove');

    // Expenses — nested di bawah event
    Route::get('/events/{event}/expenses/create',       [ExpenseController::class, 'create'])->name('events.expenses.create');
    Route::post('/events/{event}/expenses',             [ExpenseController::class, 'store'])->name('events.expenses.store');

    // Tandai Lunas — debtor bayar ke creditor dalam 1 event
    Route::post('/events/{event}/pay/{creditor}',       [ExpenseSplitController::class, 'markPaid'])->name('events.pay');

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Stub pages
    Route::get('/friends', fn() => view('friends.index'))->name('friends.index');
    Route::get('/bills',   fn() => view('bills.index'))->name('bills.index');
});

require __DIR__.'/auth.php';
