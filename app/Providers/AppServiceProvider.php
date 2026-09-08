<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.app', function ($view) {
            $route = request()->route()?->getName();
            $placement = match (true) {
                $route === 'home' => 'home',
                $route === 'frontend.section' => 'sections',
                $route === 'frontend.sub_section' => 'subsections',
                $route === 'frontend.category' => 'categories',
                $route === 'about' => 'about',
                $route === 'contact' => 'contact',
                str_starts_with((string) $route, 'account.') => 'account',
                in_array($route, ['theory.mock_test_info', 'theory.mock_test_result', 'theory.dynamic_mock_info', 'theory.hazard_mock_info', 'theory.hazard_mock_result'], true) => 'mock_tests',
                default => null,
            };
            $languageId = auth('web')->user()?->preferred_language_id ?: Language::query()->where('code', 'en')->value('id');
            $siteAd = $placement ? Ad::currentlyRunning()->where('display_type', 'site')->forLanguage($languageId)->whereJsonContains('placements', $placement)->inRandomOrder()->first() : null;
            $whatsappPlacement = match (true) {
                $route === 'home' => 'home',
                $route === 'frontend.section' => 'sections',
                $route === 'frontend.sub_section' => 'subsections',
                $route === 'frontend.category' => 'categories',
                $route === 'theory.practice' => 'practice',
                in_array($route, ['theory.hazard_library', 'theory.hazard_study'], true) => 'learning',
                str_starts_with((string) $route, 'theory.mock_') || str_starts_with((string) $route, 'theory.dynamic_mock_') || str_starts_with((string) $route, 'theory.hazard_mock_') => 'mock_tests',
                str_starts_with((string) $route, 'account.') || $route === 'history' => 'account',
                in_array($route, ['login', 'register', 'password.request', 'password.reset', 'verification.notice'], true) => 'auth',
                $route === 'about' => 'about',
                $route === 'contact' => 'contact',
                default => null,
            };
            $whatsappNumber = SiteSetting::valueFor('whatsapp_number', '');
            $savedWhatsappPlacements = SiteSetting::valueFor('whatsapp_placements');
            $whatsappPlacements = $savedWhatsappPlacements === null
                ? ['sections', 'subsections']
                : (json_decode((string) $savedWhatsappPlacements, true) ?: []);
            $showWhatsappButton = $whatsappNumber && $whatsappPlacement && in_array($whatsappPlacement, $whatsappPlacements, true);

            $view->with(compact('siteAd', 'whatsappNumber', 'showWhatsappButton'));
        });
    }
}
