<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicTicketController;

Route::get('/', [PublicTicketController::class, 'index'])->name('home');
Route::post('/laporan-store', [PublicTicketController::class, 'store'])->name('laporan.store');
Route::get('/laporan-sukses/{uuid}', [PublicTicketController::class, 'success'])->name('laporan.sukses');
Route::get('/laporan/cek', [PublicTicketController::class, 'cek'])->name('laporan.cek');
Route::get('/laporan/chat-history', [PublicTicketController::class, 'chatHistory'])->name('laporan.chat-history');
Route::post('/laporan-reply/{uuid}', [PublicTicketController::class, 'reply'])->name('laporan.reply');
Route::post('/laporan-upload-trix', [PublicTicketController::class, 'uploadTrixImage'])->name('laporan.upload_trix');

// Fix: Redirect default 'login' route to Admin Login
Route::name('login')->get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
});