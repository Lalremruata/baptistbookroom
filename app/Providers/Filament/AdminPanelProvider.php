<?php

namespace App\Providers\Filament;

// use Filament\Http\Livewire\Auth\Login;
use App\Filament\Pages\Auth\Login;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\MenuItem;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Pages\Auth\EditProfile;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Swis\Filament\Backgrounds\ImageProviders\Triangles;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('')
            ->path('')
            ->login(Login::class)
            ->databaseNotifications(true)
            ->viteTheme('resources/css/filament/Admin/theme.css')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Poppins')
            ->brandName('Baptist BookRoom')
            ->favicon(asset('images/favicon.jpg'))
            ->darkMode(true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->maxContentWidth(MaxWidth::Full)
            // ->brandLogo(asset('/images/bcm-logo.svg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            // ->sidebarCollapsibleOnDesktop()
            ->topNavigation()
            // ->spa()
            ->navigationGroups([
                NavigationGroup::make()
                     ->label('Sales'),
                NavigationGroup::make()
                     ->label('Stocks')
                     ->collapsible(false),
                NavigationGroup::make()
                     ->label('Private Books')
                     ->collapsed(),
                NavigationGroup::make()
                    ->label('Manage Items')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Assets')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('Settings')
                    ->collapsed()
            ])
            ->profile(EditProfile::class)
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile'),
                // ...
            ])
            ;
    }
}
