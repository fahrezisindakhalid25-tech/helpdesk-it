<?php

namespace App\Filament\Resources\Slas\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Slas\SlaResource;
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
