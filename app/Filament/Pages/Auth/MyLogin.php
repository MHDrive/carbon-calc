<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Validation\ValidationException;

class MyLogin extends BaseLogin
{

    // Membuat Form input Login 
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('login')
                            ->label('Username / Email')
                            ->required()
                            ->autocomplete()
                            ->autofocus()
                            ->extraInputAttributes(['tabindex' => 1]),
                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/login.form.password.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->autocomplete('current-password')
                            ->required()
                            ->extraInputAttributes(['tabindex' => 2]),
                        Group::make()
                            ->schema([
                                Checkbox::make('remember')
                                    ->label(__('filament-panels::pages/auth/login.form.remember.label'))
                                    ->columnSpan(1),
                                Actions::make([
                                    Action::make('forget_password')
                                        ->link()
                                        ->label(__('filament-panels::pages/auth/login.actions.request_password_reset.label'))
                                        ->url(filament()->hasPasswordReset() ? filament()->getRequestPasswordResetUrl() : null)
                                ])
                                    ->alignRight()
                                    ->columnSpan(1)
                            ])->columns(2)

                    ])
                    ->statePath('data'),
            ),
        ];
    }

    // mengatur validasi login
    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    // membuat error validasi login
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
