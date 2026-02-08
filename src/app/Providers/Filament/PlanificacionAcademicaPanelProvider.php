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
use App\Filament\Widgets\ShortcutsWidget;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;

use Filament\Navigation\NavigationGroup;
use App\Filament\PlanificacionAcademica\Pages\Auth\PlanificacionLogin;

use App\Filament\Resources\Fichas\FichaResource;
use App\Filament\Pages\Dashboards\CronogramasDashboard;
use App\Filament\Resources\ProgramacionInstructors\ProgramacionInstructorResource;

class PlanificacionAcademicaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('planificacion')
            ->path('planificacion')
            ->login(PlanificacionLogin::class)
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandName('Planificación Académica')
            ->brandLogo(asset('images/logo-cata.png'))
            ->darkModeBrandLogo(asset('images/logo-cata-dark.png'))
            ->brandLogoHeight('2.5rem')
            ->discoverResources(in: app_path('Filament/PlanificacionAcademica/Resources'), for: 'App\Filament\PlanificacionAcademica\Resources')
            ->resources([
                FichaResource::class,
                ProgramacionInstructorResource::class,
            ])
            ->discoverPages(in: app_path('Filament/PlanificacionAcademica/Pages'), for: 'App\Filament\PlanificacionAcademica\Pages')
            ->pages([
                Dashboard::class,
                CronogramasDashboard::class,
                
            ])
            ->discoverWidgets(in: app_path('Filament/PlanificacionAcademica/Widgets'), for: 'App\Filament\PlanificacionAcademica\Widgets')
            ->widgets([
                AccountWidget::class,
                CronosenaInfoWidget::class,
                ShortcutsWidget::class,
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
                    ->label('programación')
                    ->icon('heroicon-o-calendar')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('fichas')
                    ->icon('heroicon-o-pencil'),

                NavigationGroup::make()
                    ->label('programas')
                    ->icon('heroicon-o-book-open')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('instructores')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('sistema')
                    ->collapsed(),
            ])

            ->viteTheme([
                'resources/css/filament/theme.css',
                'resources/js/app.js',
            ])

            //->topNavigation() //Habilitar la barra de navegación superior

            ->sidebarCollapsibleOnDesktop()
            //->spa() //Habilitar la aplicación de una sola página (SPA)
            ->unsavedChangesAlerts()
            //->sidebarFullyCollapsibleOnDesktop() //Contraer la barra lateral completamente
        ;
    }
}
