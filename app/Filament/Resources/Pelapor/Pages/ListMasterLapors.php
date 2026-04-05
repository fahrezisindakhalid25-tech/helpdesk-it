<?php

namespace App\Filament\Resources\Pelapor\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Pelapor\PelaporResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasterLapors extends ListRecords
{
    protected static string $resource = PelaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
