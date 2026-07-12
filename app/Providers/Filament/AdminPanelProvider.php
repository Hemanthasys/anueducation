<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Helpers\ThemeHelper;
use App\Http\Middleware\FilamentAdminAccess;
use App\Http\Middleware\MustChangePasswordAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\AdminHelpWidget;
use App\Filament\Widgets\WelcomeWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->favicon(
                \App\Models\SiteSetting::get('favicon')
                    ? asset('storage/' . \App\Models\SiteSetting::get('favicon'))
                    : asset('images/favicon.png')
            )
            ->brandLogo(fn () => asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->brandName(config('app.name'))
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                function () {
                    $theme = ThemeHelper::getTheme();

                    return new HtmlString(<<<HTML
                        <style>
                            .fi-simple-layout {
                                background: linear-gradient(135deg, {$theme['dark']} 0%, {$theme['primary']} 100%);
                            }
                            .fi-simple-header {
                                display: none;
                            }
                        </style>
                    HTML);
                }
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_PAGE_START,
                function () {
                    $theme    = ThemeHelper::getTheme();
                    $emblem   = asset('images/emblem.png');
                    $logo     = asset('images/logo.png');
                    $flag     = asset('images/flag.png');
                    $appName  = config('app.name');

                    return new HtmlString(<<<HTML
                        <div style="max-width:24rem;margin:0.25rem auto 1.25rem;text-align:center;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:14px;margin-bottom:14px;">
                                <img src="{$emblem}" alt="Emblem" style="height:52px;width:auto;object-fit:contain;">
                                <img src="{$logo}" alt="Logo" style="height:58px;width:auto;object-fit:contain;">
                                <img src="{$flag}" alt="Flag" style="height:40px;width:auto;object-fit:contain;border-radius:3px;">
                            </div>
                            <h1 style="font-size:1.05rem;font-weight:700;color:{$theme['primary']};margin:0 0 4px;">{$appName}</h1>
                            <p style="font-size:0.85rem;color:#6b7280;margin:0;">Digital Education Management Platform</p>
                        </div>
                    HTML);
                }
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                function () {
                    $theme   = ThemeHelper::getTheme();
                    $homeUrl = url('/');
                    $label   = app()->getLocale() === 'si' ? 'ප්‍රධාන වෙබ් අඩවියට ආපසු' : 'Back to Main Site';

                    return new HtmlString(<<<HTML
                        <div style="text-align:center;margin-top:1.25rem;">
                            <a href="{$homeUrl}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.85rem;font-weight:600;color:{$theme['primary']};text-decoration:none;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:1rem;height:1rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                </svg>
                                {$label}
                            </a>
                        </div>
                    HTML);
                }
            )
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                WelcomeWidget::class,
                AdminHelpWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                FilamentAdminAccess::class,
                MustChangePasswordAdmin::class,
            ]);
    }
}