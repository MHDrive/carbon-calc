<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Get;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;

class MyRegister extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        // $this->getRoleFormComponent(), 
                    ])
                    ->statePath('data'),
            ),
        ];
    }
    protected function getNameFormComponent(): Component
    {
        return Group::make()
            ->schema([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->minLength(3)
                    ->maxLength(255),
                TextInput::make('username')
                    ->label('Username')
                    ->minLength(3)
                    ->maxLength(255),
            ]);
    }
    protected function handleRegistration(array $data): Model
    {
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'] ?: '';
        return $this->getUserModel()::create($data);
    }
}
