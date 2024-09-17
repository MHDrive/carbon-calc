<?php

namespace App\Filament\Resources\RecapitulationResource\Pages;

use App\Filament\Resources\RecapitulationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecapitulation extends EditRecord
{
    protected static string $resource = RecapitulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
