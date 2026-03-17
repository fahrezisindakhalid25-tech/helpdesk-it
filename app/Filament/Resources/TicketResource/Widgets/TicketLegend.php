<?php

namespace App\Filament\Resources\TicketResource\Widgets;

use Filament\Widgets\Widget;

class TicketLegend extends Widget
{
    protected string $view = 'filament.widgets.ticket-legend';
    
    protected int | string | array $columnSpan = 'full';
}
