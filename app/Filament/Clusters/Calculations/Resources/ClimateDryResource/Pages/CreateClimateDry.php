<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;

class CreateClimateDry extends CreateRecord
{
    protected static string $resource = ClimateDryResource::class;

    public $newData;

    protected function handleRecordCreation(array $data): Model
    {
        dd($data);
        $record = static::getModel()::create($data);
        $this->newData = $record;
        return $record;
    }
}
