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

        $total = array_sum($data);
        $labels = array_map(function($status, $count) use ($total) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            return "$status ($percentage% - $count)";
        }, array_keys($data), array_values($data));

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
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'datalabels' => [
                    'color' => '#ffffff',
                    'font' => [
                        'weight' => 'bold',
                        'size' => 12,
                    ],
                ],
            ],
        ];
    }
}
