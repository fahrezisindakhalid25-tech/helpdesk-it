<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicTicketController;

Route::get('/', [PublicTicketController::class, 'index'])->name('home');
Route::post('/laporan-store', [PublicTicketController::class, 'store'])->name('laporan.store');
Route::get('/laporan-sukses/{uuid}', [PublicTicketController::class, 'success'])->name('laporan.sukses');
Route::get('/laporan/cek', [PublicTicketController::class, 'cek'])->name('laporan.cek');
Route::post('/laporan-reply/{uuid}', [PublicTicketController::class, 'reply'])->name('laporan.reply');