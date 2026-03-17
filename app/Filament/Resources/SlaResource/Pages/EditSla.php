<?php

namespace App\Filament\Resources\SlaResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\SlaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSla extends EditRecord
{
    protected static string $resource = SlaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
