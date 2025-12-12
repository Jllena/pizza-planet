<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Livewire\OrderCreate;
use Illuminate\Support\Facades\Route;

Route::get('/', OrderCreate::class)->name('orders.create');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
