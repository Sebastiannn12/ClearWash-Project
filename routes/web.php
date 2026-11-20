<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipSettingController;
use App\Http\Controllers\MembershipTransactionController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemberController::class, 'index']);

Route::patch('/members/{member}/reactivate', [MemberController::class, 'reactivate'])
    ->name('members.reactivate');
Route::delete('/members/{member}/delete', [MemberController::class, 'softDelete'])
    ->name('members.delete');
Route::delete('/members/{member}/permanent', [MemberController::class, 'forceDelete'])
    ->withTrashed()
    ->name('members.force-delete');
Route::patch('/members/{member}/restore', [MemberController::class, 'restore'])
    ->withTrashed()
    ->name('members.restore');
Route::resource('members', MemberController::class)->except(['destroy']);
Route::delete('/members/{member}', [MemberController::class, 'destroy'])
    ->name('members.destroy');

Route::get('/membership-setup', [MembershipSettingController::class, 'index'])
    ->name('membership-settings.index');
Route::patch('/membership-setup/levels/{membershipLevel}', [MembershipSettingController::class, 'updateLevel'])
    ->name('membership-settings.levels.update');

Route::resource('transactions', MembershipTransactionController::class)
    ->only(['index', 'create', 'store', 'show']);

Route::post('/transactions/{transaction}/payments', [PaymentController::class, 'store'])
    ->name('transactions.payments.store');

Route::patch('/transactions/{transaction}/approve', [MembershipTransactionController::class, 'approve'])
    ->name('transactions.approve');
Route::patch('/transactions/{transaction}/cancel', [MembershipTransactionController::class, 'cancel'])
    ->name('transactions.cancel');
