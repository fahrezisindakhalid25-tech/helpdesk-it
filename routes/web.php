<?php

use App\Http\Controllers\PublicTicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicTicketController::class, 'index'])->name('home');
Route::middleware('throttle:5,1')->post('/laporan-store', [PublicTicketController::class, 'store'])->name('laporan.store');
Route::get('/laporan-sukses/{uuid}', [PublicTicketController::class, 'success'])->name('laporan.sukses');
Route::middleware('throttle:30,1')->get('/laporan/cek', [PublicTicketController::class, 'cek'])->name('laporan.cek');
Route::middleware('throttle:5,1')->post('/laporan/akses/{uuid}', [PublicTicketController::class, 'authorizeAccess'])->name('laporan.authorize');
Route::middleware('throttle:90,1')->get('/laporan/chat-history', [PublicTicketController::class, 'chatHistory'])->name('laporan.chat-history');
Route::middleware('throttle:12,1')->post('/laporan-reply/{uuid}', [PublicTicketController::class, 'reply'])->name('laporan.reply');
Route::middleware('throttle:12,1')->post('/laporan-upload-trix', [PublicTicketController::class, 'uploadTrixImage'])->name('laporan.upload_trix');

// Fix: Redirect default 'login' route to Admin Login
Route::name('login')->get('/login', function () {
    return redirect()->route('filament.admin.auth.login');
});
