<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateWetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClimateWet extends EditRecord
{
    protected static string $resource = ClimateWetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
