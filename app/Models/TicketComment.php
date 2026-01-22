<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    protected $guarded = []; // Izinkan semua data masuk

    // Relasi: Komentar ini milik User siapa?
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}