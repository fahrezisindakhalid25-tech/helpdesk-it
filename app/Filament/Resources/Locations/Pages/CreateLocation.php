<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Pages\CreateRecord;
use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;
}
