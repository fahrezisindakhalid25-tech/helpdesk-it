<?php

namespace App\Filament\Pages;

use Filament\Resources\Pages\CreateRecord as BaseCreateRecord;
use Filament\Support\Icons\Heroicon;

class CreateRecord extends BaseCreateRecord
{
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->icon(Heroicon::PaperAirplane),
            ...($this->canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }
}
