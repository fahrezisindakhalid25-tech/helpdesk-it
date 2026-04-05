<?php

namespace App\Filament\Resources\Slas\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Slas\SlaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSlas extends ListRecords
{
    protected static string $resource = SlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
