<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Tiket';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Ticket::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tiket',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#fbbf24', // Open (Warning)
                        '#3b82f6', // Replied (Info)
                        '#22c55e', // Solved (Success)
                        '#ef4444', // Closed (Danger)
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
