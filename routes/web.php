<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemberController::class, 'index']);

Route::patch('/members/{member}/reactivate', [MemberController::class, 'reactivate'])
    ->name('members.reactivate');
Route::delete('/members/{member}/permanent', [MemberController::class, 'forceDelete'])
    ->name('members.force-delete');
Route::resource('members', MemberController::class)->except(['destroy']);
Route::delete('/members/{member}', [MemberController::class, 'destroy'])
    ->name('members.destroy');
