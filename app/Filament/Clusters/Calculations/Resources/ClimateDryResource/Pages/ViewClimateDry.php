<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;

class ViewClimateDry extends ViewRecord
// implements HasTable
{
    // use InteractsWithTable;
    protected static string $resource = ClimateDryResource::class;

    protected static ?string $title = 'Hasil Kalkulator';

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
