<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Filament\Support\RawJs;

class TicketStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Tiket';
    protected static ?int $sort = 2;
    protected static string $view = 'filament.widgets.chart-widget-custom';

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
                    'data' => [
                        $data['Open'] ?? 0,
                        $data['Replied'] ?? 0,
                        $data['Solved'] ?? 0,
                        $data['Closed'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#fbbf24', // Yellow - Open
                        '#3b82f6', // Blue - Replied
                        '#22c55e', // Green - Solved
                        '#ef4444', // Red - Closed
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Open', 'Replied', 'Solved', 'Closed'],
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
                'legend' => ['display' => false],
                'tooltip' => ['enabled' => true],
            ],
        ];
    }
}
