<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Filament\Support\RawJs;

class FirstResponseSlaChart extends ChartWidget
{
    protected static ?string $heading = 'SLA First Response';
    protected static ?int $sort = 1;
    protected static string $view = 'filament.widgets.chart-widget-custom';

    protected function getData(): array
    {
        $tickets = Ticket::whereNotNull('sla_id')->get();

        $onTime = 0;
        $overdue = 0;
        $running = 0; 

        foreach ($tickets as $ticket) {
            $sla = $ticket->sla;
            if (!$sla) continue;

            $slaDays = (int) $sla->response_days;
            $timeParts = explode(':', $sla->response_time ?? '00:00:00');
            
            $deadline = Carbon::parse($ticket->created_at)
                ->addDays($slaDays)
                ->addHours((int)$timeParts[0])
                ->addMinutes((int)$timeParts[1]);

            if ($ticket->replied_at) {
                if (Carbon::parse($ticket->replied_at)->lte($deadline)) {
                    $onTime++;
                } else {
                    $overdue++;
                }
            } else {
                if (now()->gt($deadline)) {
                    $overdue++;
                } else {
                    $running++;
                }
            }
        }

        $total = $onTime + $overdue + $running;
        $formatLabel = fn($label, $val) => $label . ' (' . ($total > 0 ? round(($val / $total) * 100, 1) : 0) . '% - ' . $val . ')';

        return [
            'datasets' => [
                [
                    'label' => 'SLA Kinerja',
                    'data' => [$onTime, $overdue, $running],
                    'backgroundColor' => ['#22c55e', '#ef4444', '#94a3b8'],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => [
                $formatLabel('On Time', $onTime), 
                $formatLabel('Overdue', $overdue), 
                $formatLabel('Dalam Proses', $running)
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
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
