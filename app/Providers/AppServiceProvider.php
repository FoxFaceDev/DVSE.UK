<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\Language;
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
            $view->with('siteAd', $siteAd);
        });
    }
}
