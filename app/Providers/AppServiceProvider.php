<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        Paginator::useBootstrapFive();

        // Force HTTPS only if NOT on localhost and APP_URL is https
        if (str_starts_with(config('app.url'), 'https') && !in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            URL::forceScheme('https');
        }

        // Share site settings globally across all views
        view()->composer('*', function ($view) {
            $view->with('siteLogo', \App\Models\Setting::get('site_logo_path'));
            $view->with('siteName', \App\Models\Setting::get('site_name', 'DigiRepo'));
            $view->with('siteInstitution', \App\Models\Setting::get('site_institution', 'Universitas'));
            $view->with('siteHeroTitle', \App\Models\Setting::get('site_hero_title', ''));
            $view->with('siteTagline', \App\Models\Setting::get('site_tagline', 'Sistem Repositori Digital Perpustakaan'));
            $view->with('siteAddress', \App\Models\Setting::get('site_address', 'Jl. Kampus Terpadu No. 1'));
            $view->with('siteEmail', \App\Models\Setting::get('site_email', 'library@institution.ac.id'));
            $view->with('siteFooter', \App\Models\Setting::get('site_footer_text', 'DigiRepo System. All rights reserved.'));
            $view->with('siteFavicon', \App\Models\Setting::get('site_favicon_path'));
        });

        // Sync is_verified when email is verified
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Verified::class,
            function ($event) {
                $event->user->update(['is_verified' => true]);
            }
        );
    }
}
