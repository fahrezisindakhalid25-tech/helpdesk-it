<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class FirstResponseSlaChart extends ChartWidget
{
    protected static ?string $heading = 'SLA First Response';
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Ambil SEMUA tiket yang punya SLA (baik yang sudah dibalas maupun belum)
        $tickets = Ticket::whereNotNull('sla_id')->get();

        $onTime = 0;
        $overdue = 0;
        // Opsional: Jika ingin menghitung yang masih berjalan (belum deadline)
        $running = 0; 

        foreach ($tickets as $ticket) {
            $sla = $ticket->sla;
            if (!$sla) continue;

            // Hitung Deadline
            $slaDays = (int) $sla->response_days;
            $timeParts = explode(':', $sla->response_time ?? '00:00:00');
            
            $deadline = Carbon::parse($ticket->created_at)
                ->addDays($slaDays)
                ->addHours((int)$timeParts[0])
                ->addMinutes((int)$timeParts[1]);

            // Cek Status
            if ($ticket->replied_at) {
                // SUDAH DIBALAS
                if (Carbon::parse($ticket->replied_at)->lte($deadline)) {
                    $onTime++;
                } else {
                    $overdue++;
                }
            } else {
                // BELUM DIBALAS
                if (now()->gt($deadline)) {
                    // Sudah lewat deadline tapi belum dibalas -> OVERDUE
                    $overdue++;
                } else {
                    // Masih dalam periode SLA (Belum telat)
                    // Tidak dimasukkan ke overdue, bisa masuk kategori 'Pending' kalau mau
                    // Untuk saat ini kita fokus ke On Time vs Overdue sesuai request
                    $running++;
                }
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'SLA Kinerja',
                    'data' => [$onTime, $overdue, $running], // Tambah Running jika ingin ditampilkan
                    'backgroundColor' => [
                        '#22c55e', // On Time (Green)
                        '#ef4444', // Overdue (Red)
                        '#94a3b8', // Running (Gray) - Opsional
                    ],
                ],
            ],
            'labels' => ['On Time', 'Overdue', 'Dalam Proses'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
