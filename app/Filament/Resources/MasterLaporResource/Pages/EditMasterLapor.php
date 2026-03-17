<?php

namespace App\Filament\Resources\MasterLaporResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\MasterLaporResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasterLapor extends EditRecord
{
    protected static string $resource = MasterLaporResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
