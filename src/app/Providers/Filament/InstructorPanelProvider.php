<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\CronosenaInfoWidget;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Instructor\Pages\Auth\EditProfile;
use App\Filament\Instructor\Pages\Auth\InstructorLogin;

use Filament\Navigation\NavigationGroup;

class InstructorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('instructor')
            ->path('instructor')
            ->login(InstructorLogin::class)
            ->profile(EditProfile::class, isSimple: false)
            ->passwordReset()
            ->viteTheme('resources/css/filament/theme.css')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandName('instructor')
            ->brandLogo(asset('images/logo-cata.png'))
            ->darkModeBrandLogo(asset('images/logo-cata-dark.png'))
            //->brandLogo(fn () => view('filament.admin.logo'))
            //->darkModeBrandLogo(fn () => view('filament.admin.logo-dark'))
            ->brandLogoHeight('2.5rem')
            ->discoverResources(in: app_path('Filament/Instructor/Resources'), for: 'App\Filament\Instructor\Resources')
            ->discoverPages(in: app_path('Filament/Instructor/Pages'), for: 'App\Filament\Instructor\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Instructor/Widgets'), for: 'App\Filament\Instructor\Widgets')
            ->widgets([
                AccountWidget::class,
                CronosenaInfoWidget::class,
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
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('gestion academica')
                    ->icon('heroicon-o-calendar')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('programas')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(), //contraible deshabilitado -> false
                NavigationGroup::make()
                    ->label('fichas')
                    ->icon('heroicon-o-pencil'),
                NavigationGroup::make()
                    ->label('instructores')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('sistema')
                    ->collapsed(),
            ])

            //->topNavigation() //Habilitar la barra de navegación superior

            ->sidebarCollapsibleOnDesktop()
            /*
            ->assets([
                Css::make('custom-stylesheet', resource_path('css/custom.css')),
                //Js::make('custom-script', resource_path('js/custom.js')),
            ])
                ;*/;
            
            
    }
}
