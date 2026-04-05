<?php

namespace App\Filament\Resources\Slas\Pages;

use App\Filament\Resources\Slas\SlaResource;
use Filament\Actions\CreateAction;
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
