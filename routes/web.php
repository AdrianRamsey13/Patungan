<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseSplitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard (atau login jika belum auth)
Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Events
    Route::resource('events', EventController::class)
        ->except(['index']);

    // Expenses — nested di bawah event
    Route::get('/events/{event}/expenses/create', [ExpenseController::class, 'create'])
        ->name('events.expenses.create');
    Route::post('/events/{event}/expenses', [ExpenseController::class, 'store'])
        ->name('events.expenses.store');

    // Tandai lunas
    Route::post('/expense-splits/{expenseSplit}/pay', [ExpenseSplitController::class, 'markPaid'])
        ->name('expense-splits.pay');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
