<?php

namespace App\Filament\Resources\Pelapor\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Pelapor\PelaporResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterLapor extends EditRecord
{
    protected static string $resource = PelaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
