<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Enums\SLAType;
use App\Filament\Pages\CreateRecord;
use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);
        $record->serviceLevelAgreements()->delete();
        $record->serviceLevelAgreements()->create(['type' => SLAType::RESPONSE, 'timeunit' => $data['response_time']]);
        $record->serviceLevelAgreements()->create(['type' => SLAType::RESOLUTION, 'timeunit' => $data['resolution_time']]);

        return $record;
    }
}
