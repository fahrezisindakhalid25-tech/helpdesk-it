<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Enums\SLAType;
use App\Filament\Pages\EditRecord;
use App\Filament\Resources\Categories\CategoryResource;
use Illuminate\Database\Eloquent\Model;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['response_time'] = $this->record->serviceLevelAgreements()->where('type', SLAType::RESPONSE)->first()?->timeunit;
        $data['resolution_time'] = $this->record->serviceLevelAgreements()->where('type', SLAType::RESOLUTION)->first()?->timeunit;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->serviceLevelAgreements()->delete();
        $record->serviceLevelAgreements()->create(['type' => SLAType::RESPONSE, 'timeunit' => $data['response_time']]);
        $record->serviceLevelAgreements()->create(['type' => SLAType::RESOLUTION, 'timeunit' => $data['resolution_time']]);

        return $record;
    }
}
