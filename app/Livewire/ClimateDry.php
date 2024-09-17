<?php

namespace App\Livewire;

use App\Models\ClimateDry as ModelsClimateDry;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;

class ClimateDry extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public $record;

    public function mount(ModelsClimateDry $record): void
    {
        $this->record = $record;
    }
        
    public function render()
    {
        return view('livewire.climate-dry');
    }
}
