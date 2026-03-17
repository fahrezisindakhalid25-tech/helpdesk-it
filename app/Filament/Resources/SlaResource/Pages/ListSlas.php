<?php

namespace App\Filament\Resources\SlaResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\SlaResource;
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
