<?php

namespace App\Filament\Resources\MasterLaporResource\Pages;

use App\Filament\Resources\MasterLaporResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterLapor extends EditRecord
{
    protected static string $resource = MasterLaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
