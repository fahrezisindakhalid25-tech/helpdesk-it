<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    // Izinkan semua kolom diisi
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (empty($model->no_tiket)) {
                $model->no_tiket = 'TICKET-' . strtoupper(Str::random(5));
            }

            // === AUTO-ASSIGN SLA DARI MASTER ===
            // Jika sla_id belum diisi, ambil dari master SLA dengan nama "first response"
            if (empty($model->sla_id)) {
                $firstResponseSla = Sla::where('name', 'first response')->first();
                if ($firstResponseSla) {
                    $model->sla_id = $firstResponseSla->id;
                }
            }

            // Jika resolution_sla_id belum diisi, ambil dari master SLA dengan nama "resolution"
            if (empty($model->resolution_sla_id)) {
                $resolutionSla = Sla::where('name', 'resolution')->first();
                if ($resolutionSla) {
                    $model->resolution_sla_id = $resolutionSla->id;
                }
            }
        });
    }

    // === RELASI ===

    // 1. Relasi SLA untuk FIRST RESPONSE (Default)
    public function sla()
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

    // 2. Relasi SLA untuk RESOLUTION (Yang bikin error tadi)
    public function resolutionSla()
    {
        return $this->belongsTo(Sla::class, 'resolution_sla_id');
    }

    // 3. Relasi Komentar
    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    // === HELPER ===
    public function isClosed()
    {
        return $this->status === 'Closed';
    }
}