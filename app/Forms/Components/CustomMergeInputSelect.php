<?php

namespace App\Forms\Components;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\HasComponents;

class CustomMergeInputSelect extends TextInput
{

    protected string $view = 'filament.forms.components.custom-merge-input-select';
    
}
