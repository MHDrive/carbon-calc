<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Get;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Auth\Register as BaseRegister;

class MyRegister extends BaseRegister
{
    // membuat form register
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(50)
                            ->autofocus(),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->minLength(3)
                            ->maxLength(50),
                        TextInput::make('username')
                            ->label('Username')
                            ->minLength(3)
                            ->maxLength(105),
                        TextInput::make('email')
                            ->label(__('filament-panels::pages/auth/register.form.email.label'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique($this->getUserModel()),
                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/register.form.password.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->rule(Password::default())
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->same('passwordConfirmation')
                            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->dehydrated(false),
                        // $this->getRoleFormComponent(), 
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    // Menangani Proses Register
    protected function handleRegistration(array $data): Model
    {
        $data['name'] = $data['first_name'] . ' ' . $data['last_name'] ?: '';
        return $this->getUserModel()::create($data);
    }
}
