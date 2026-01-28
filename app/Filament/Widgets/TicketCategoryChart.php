<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Tiket per Kategori';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Ticket::selectRaw('topik_bantuan, count(*) as total')
            ->groupBy('topik_bantuan')
            ->pluck('total', 'topik_bantuan')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket',
                    'data' => array_values($data),
                    'backgroundColor' => '#3b82f6',
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
            'indexAxis' => 'y', // Horizontal Bar
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                // Tetap biarkan datalabels simple aktif karena user sudah install pluginnya di app.js, 
                // tapi tidak usah pakai formatter aneh-aneh.
                'datalabels' => [
                    'color' => '#ffffff',
                    'anchor' => 'end',
                    'align' => 'start',
                    'offset' => -4,
                    'font' => [
                        'weight' => 'bold',
                        'size' => 12,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'precision' => 0, // Integer only
                    ],
                ],
                'y' => [
                    'display' => true, // Tampilkan kembali label defaultnya
                ],
            ],
        ];
    }
}
