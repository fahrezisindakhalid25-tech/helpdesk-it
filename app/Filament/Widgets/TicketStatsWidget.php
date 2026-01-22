<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTicket = Ticket::count();
        $openTicket = Ticket::where('status', 'Open')->count();
        $repliedTicket = Ticket::where('status', 'Replied')->count();
        $solvedTicket = Ticket::where('status', 'Solved')->count();
        $closedTicket = Ticket::where('status', 'Closed')->count();

        return [
            Stat::make('Total Ticket', $totalTicket)
                ->description('Semua tiket yang masuk')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Open', $openTicket)
                ->description('Tiket aktif')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Replied', $repliedTicket)
                ->description('Admin sudah merespon')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Solved', $solvedTicket)
                ->description('Masalah terselesaikan')
                ->descriptionIcon('heroicon-m-check')
                ->color('success'),

            Stat::make('Closed', $closedTicket)
                ->description('Tiket ditutup')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
