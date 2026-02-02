<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use App\Models\Location;

class TicketLocationChart extends ChartWidget
{
    protected static ?string $heading = 'Tiket per Lokasi';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    // Gunakan custom view agar ada label manualnya
    // Gunakan custom view agar ada label manualnya
    protected static string $view = 'filament.widgets.chart-widget-custom';
    protected static ?string $maxHeight = '1200px';

    protected function getData(): array
    {
        // ... (data fetching logic remains same) ...
        $allLocations = Location::pluck('name')->toArray();
        
        $ticketCounts = Ticket::selectRaw('lokasi, count(*) as total')
            ->groupBy('lokasi')
            ->pluck('total', 'lokasi')
            ->toArray();

        $data = [];
        foreach ($allLocations as $loc) {
            $data[$loc] = $ticketCounts[$loc] ?? 0;
        }
        arsort($data); // Urutkan dari yang terbanyak

        // Ambil Top 15 agar tidak terlalu padat jika banyak
        arsort($data);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket',
                    'data' => array_values($data),
                    'backgroundColor' => '#ec4899', // Pink
                    'borderRadius' => 4,
                    'barPercentage' => 0.9,
                    'categoryPercentage' => 0.9,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'indexAxis' => 'y', // Horizontal Bar
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'datalabels' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'display' => true,
                ],
                'x' => [
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'layout' => [
                'padding' => [
                    'right' => 50,
                ],
            ],
        ];
    }
}
