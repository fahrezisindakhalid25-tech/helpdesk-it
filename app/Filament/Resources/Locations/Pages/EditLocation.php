<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Pages\EditRecord;
use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions;
use Filament\Actions\DeleteAction;

class EditLocation extends EditRecord
{
    protected static string $resource = LocationResource::class;
}
