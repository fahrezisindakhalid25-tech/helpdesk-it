<?php

namespace App\Filament\Widgets;

use App\Models\Master\Category;
use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Tiket per Kategori';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected string $view = 'filament.widgets.chart-widget-custom';

    // Perbesar area canvas agar batang tidak gepeng
    protected ?string $maxHeight = '1200px';

    protected function getData(): array
    {
        // 1. Ambil semua kategori yang mungkin
        $allCategories = Category::pluck('name')->toArray();

        // 2. Ambil hitungan tiket per kategori dari Database
        $ticketCounts = Ticket::selectRaw('topik_bantuan, count(*) as total')
            ->groupBy('topik_bantuan')
            ->pluck('total', 'topik_bantuan')
            ->toArray();

        // 3. Merge data: Pastikan semua kategori ada, jika tidak ada set 0
        $data = [];
        foreach ($allCategories as $category) {
            $data[$category] = $ticketCounts[$category] ?? 0;
        }

        // Urutkan dari yang terbanyak (High to Low)
        arsort($data);

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tiket',
                    'data' => array_values($data),
                    'backgroundColor' => '#3b82f6',
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
                'x' => [
                    'ticks' => [
                        'precision' => 0, // Integer only
                    ],
                ],
                'y' => [
                    'display' => true, // Tampilkan kembali label defaultnya
                ],
            ],
            'layout' => [
                'padding' => [
                    'right' => 50, // Ruang untuk label yang 'Outside'
                ],
            ],
        ];
    }
}
