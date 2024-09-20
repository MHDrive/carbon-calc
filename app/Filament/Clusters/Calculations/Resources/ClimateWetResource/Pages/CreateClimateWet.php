<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource;

class CreateClimateWet extends CreateRecord
{
    protected static string $resource = ClimateWetResource::class;

    public $newData;
    protected function handleRecordCreation(array $data): Model
    {
        dd($data);
        $record = static::getModel()::create($data);
        $this->newData = $record;
        return $record;
    }

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('view-guest', ['record' => $this->newData->id]);
    // }
}
