<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Ticket extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'resolution_due_at' => 'datetime',
        'replied_at' => 'datetime',
        'solved_at' => 'datetime',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

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

            // Set SLA First Response
            if (empty($model->sla_id)) {
                $firstResponseSla = Sla::where('name', 'LIKE', 'first response')->first();
                if ($firstResponseSla) {
                    $model->sla_id = $firstResponseSla->id;

                    $time = Carbon::parse($firstResponseSla->response_time);
                    $model->sla_due_at = now()
                        ->addDays((int) $firstResponseSla->response_days)
                        ->addHours($time->hour)
                        ->addMinutes($time->minute);
                }
            }

            // Set SLA Resolution
            if (empty($model->resolution_sla_id)) {
                $resolutionSla = Sla::where('name', 'LIKE', 'resolution')->first();
                if ($resolutionSla) {
                    $model->resolution_sla_id = $resolutionSla->id;

                    $timeRes = Carbon::parse($resolutionSla->response_time);
                    $model->resolution_due_at = now()
                        ->addDays((int) $resolutionSla->response_days)
                        ->addHours($timeRes->hour)
                        ->addMinutes($timeRes->minute);
                }
            }
        });
    }

    public function sla()
    {
        return $this->belongsTo(Sla::class, 'sla_id');
    }

    public function resolutionSla()
    {
        return $this->belongsTo(Sla::class, 'resolution_sla_id');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function isClosed()
    {
        return $this->status === 'Closed';
    }
}