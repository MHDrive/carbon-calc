<?php

namespace App\Filament\Resources\RecapitulationResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ViewEntry;
use App\Filament\Resources\RecapitulationResource;

class viewRecapitulation extends ViewRecord
{
    protected static string $resource = RecapitulationResource::class;

    protected static ?string $title = 'Detail Rekapitulasi';

    public function getBreadcrumb(): string
    {
        return 'Detail';
    }

    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->schema([
                Section::make()
                    ->schema([
                        ViewEntry::make('info')
                            ->hiddenLabel()
                            ->viewData([
                                'row' => $this->getRecord(),
                            ])
                            ->view('filament.infolists.components.view-recapitulation'),
                    ])
            ])
            ->record($this->getRecord())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }
}
