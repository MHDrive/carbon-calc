<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\ProfileUser;
use App\Filament\Pages\Auth\MyLogin;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Pages\Auth\MyRegister;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentIcon;
use App\Filament\Resources\SubmissionResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Auth\RequestPasswordReset;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentIcon::register([
            'panels::topbar.group.toggle-button' => 'heroicon-o-bars-3',
            // 'panels::sidebar.expand-button' => 'heroicon-o-bars-3',
        ]);
    }
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->unsavedChangesAlerts()
            // ->spaUrlExceptions(fn (): array => [
            //     url('/admin'),
            //     SubmissionResource::getUrl(),
            // ])
            ->login(MyLogin::class)
            ->registration(MyRegister::class)
            ->passwordReset(RequestPasswordReset::class)
            ->profile(ProfileUser::class, isSimple: false)
            ->brandLogo(fn() => view('filament.panels.logo'))
            ->brandLogoHeight('1rem')
            // ->sidebarFullyCollapsibleOnDesktop()
            // ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex('#66D56A'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
