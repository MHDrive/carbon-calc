<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClimateMoist extends EditRecord
{
    protected static string $resource = ClimateMoistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
