<?php

namespace App\Filament\Resources\MasterLapors\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\MasterLapors\MasterLaporResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasterLapors extends ListRecords
{
    protected static string $resource = MasterLaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
