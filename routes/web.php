<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipSettingController;
use App\Http\Controllers\MembershipTransactionController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::patch('/members/{member}/reactivate', [MemberController::class, 'reactivate'])
        ->name('members.reactivate');
    Route::delete('/members/{member}/permanent', [MemberController::class, 'forceDelete'])
        ->name('members.force-delete');
    Route::get('/members/{member}/history', [MemberController::class, 'history'])->name('members.history');
    Route::get('/members/{member}/print', [MemberController::class, 'print'])->name('members.print');
    Route::resource('members', MemberController::class)->except(['destroy']);
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])
        ->name('members.destroy');

    Route::get('/membership-setup', [MembershipSettingController::class, 'index'])
        ->name('membership-settings.index');
    Route::get('/membership-setup/categories', [MembershipSettingController::class, 'categories'])->name('membership-settings.categories.index');
    Route::get('/membership-setup/levels', [MembershipSettingController::class, 'levels'])->name('membership-settings.levels.index');
    Route::post('/membership-setup/categories', [MembershipSettingController::class, 'storeCategory'])
        ->name('membership-settings.categories.store');
    Route::patch('/membership-setup/categories/{membershipCategory}', [MembershipSettingController::class, 'updateCategory'])
        ->name('membership-settings.categories.update');
    Route::delete('/membership-setup/categories/{membershipCategory}', [MembershipSettingController::class, 'destroyCategory'])
        ->name('membership-settings.categories.destroy');
    Route::post('/membership-setup/levels', [MembershipSettingController::class, 'storeLevel'])
        ->name('membership-settings.levels.store');
    Route::patch('/membership-setup/levels/{membershipLevel}', [MembershipSettingController::class, 'updateLevel'])
        ->name('membership-settings.levels.update');
    Route::delete('/membership-setup/levels/{membershipLevel}', [MembershipSettingController::class, 'destroyLevel'])
        ->name('membership-settings.levels.destroy');

    Route::resource('transactions', MembershipTransactionController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::post('/transactions/{transaction}/payments', [PaymentController::class, 'store'])
        ->name('transactions.payments.store');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/transactions/{transaction}/payment', [PaymentController::class, 'create'])->name('payments.create');

    Route::patch('/transactions/{transaction}/approve', [MembershipTransactionController::class, 'approve'])
        ->name('transactions.approve');
    Route::patch('/transactions/{transaction}/cancel', [MembershipTransactionController::class, 'cancel'])
        ->name('transactions.cancel');
});
