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
}
