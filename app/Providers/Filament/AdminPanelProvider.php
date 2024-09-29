<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Illuminate\View\View;
use Filament\Tables\Table;
use Filament\PanelProvider;
use Filament\FilamentManager;
use Filament\Support\Assets\Css;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Enums\StatusSubmissionEnum;
use App\Filament\Pages\ProfileUser;
use Filament\View\PanelsRenderHook;
use App\Filament\Pages\Auth\MyLogin;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Section;
use App\Filament\Pages\Auth\MyRegister;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\Models\Contracts\FilamentUser;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use App\Filament\Resources\SubmissionResource;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Auth\RequestPasswordReset;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\InfoClimateDries;

class AdminPanelProvider extends PanelProvider
{

    public function boot(): void
    {
        FilamentIcon::register([
            'panels::sidebar.expand-button' => 'heroicon-o-bars-3',
        ]);
        Section::configureUsing(function (Section $section): void {
            $section->extraAttributes(['class' => 'section-change-password']);
        });
    }
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling(null)
            ->renderHook(
                'panels::sidebar.nav',
                function ($item) {
                    if (! $item->isActive()) {
                        return [
                            'attributes' => [
                                'class' => 'text-[#66799E]',
                            ],
                        ];
                    }
                }
            )
            ->navigationItems(static::getNavigationItems())
            // ->unsavedChangesAlerts()
            ->login(MyLogin::class)
            ->registration(MyRegister::class)
            ->passwordReset(RequestPasswordReset::class)
            ->profile(ProfileUser::class, isSimple: false)
            ->brandLogo(fn() => view('filament.panels.logo'))
            ->brandLogoHeight('3.5rem') // desain figma custom
            // ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::hex('#66D56A'),
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
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
            ])
            ->plugins([
                FilamentBackgroundsPlugin::make()
                ->remember(900)
                ->imageProvider(
                    MyImages::make()
                        ->directory('images/filament-background/')
                ),
            ]);
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Profil')
                ->url(fn() => route('filament.admin.auth.profile'))
                ->icon('heroicon-o-user')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.auth.profile'))
                ->hidden(fn() => (auth()->user()->isAdmin())),
            NavigationItem::make('Pengajuan Berkas')
                ->url(function () {
                    $user = auth()->user();
                    $submission  = $user->submissions();
                    $data = $submission->routeGuest()->first();
                    if ($data) {
                        return route('filament.admin.resources.submissions.view-guest', ['record' => $data->id]);
                    }
                    return route('filament.admin.resources.submissions.create');
                })
                ->icon('heroicon-o-user')
                ->isActiveWhen(fn() => request()->routeIs('filament.admin.resources.submissions.create') || request()->routeIs('filament.admin.resources.submissions.view'))
                ->hidden(fn() => (auth()->user()->isAdmin()))
                ->sort(3),
        ];
    }
}
