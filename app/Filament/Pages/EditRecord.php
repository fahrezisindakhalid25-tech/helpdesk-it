<?php

namespace App\Filament\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord as BaseEditRecord;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class EditRecord extends BaseEditRecord
{
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->icon(Heroicon::PencilSquare)
                ->color(Color::Yellow),
            $this->getCancelFormAction(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }
}
