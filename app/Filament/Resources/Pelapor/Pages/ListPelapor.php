<?php

namespace App\Filament\Resources\Pelapor\Pages;

use App\Filament\Resources\Pelapor\PelaporResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListPelapor extends ListRecords
{
    protected static string $resource = PelaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->iconButton()
                ->icon(Heroicon::PlusCircle),
        ];
    }
}
