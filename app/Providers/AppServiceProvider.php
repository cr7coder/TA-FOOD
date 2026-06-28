<?php

namespace App\Providers;

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
        if (app()->environment('production') || config('app.env') === 'production') {
            URL::forceScheme('https');
        } elseif (!app()->runningInConsole()) {
            $host = request()->header('host') ?? '';
            if (!str_contains($host, 'localhost') && !str_contains($host, '127.0.0.1')) {
                URL::forceScheme('https');
            }
        }
    }
}
