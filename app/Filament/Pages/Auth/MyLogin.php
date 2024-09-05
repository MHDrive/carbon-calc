<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Page;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\Actions\Action;

class MyLogin extends BaseLogin
{

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                        // $this->getMyAction(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    // public function getMyAction()
    // {
    //     return Actions::make([
    //         Action::make('register')
    //             ->link()
    //             ->label(__('filament-panels::pages/auth/login.actions.register.label'))
    //             ->url(filament()->getRegistrationUrl())
    //     ]);
    // }
}
