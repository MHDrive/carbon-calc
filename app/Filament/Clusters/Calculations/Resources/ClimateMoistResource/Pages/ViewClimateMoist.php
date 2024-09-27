<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ViewEntry;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource;

class ViewClimateMoist extends ViewRecord
{
    protected static string $resource = ClimateMoistResource::class;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'hasil';
    }

    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->schema([
                Section::make()
                    ->schema([
                        ViewEntry::make('info-result')
                        ->hiddenLabel()
                            ->viewData([
                                'data' => $this->getRecord(),
                            ])
                            ->view('filament.infolists.components.info-result-calculation'),
                    ])
            ])
            ->record($this->getRecord())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }
}
