<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClimateDry extends EditRecord
{
    protected static string $resource = ClimateDryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
