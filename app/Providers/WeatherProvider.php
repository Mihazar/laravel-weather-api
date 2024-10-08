<?php

namespace App\Providers;

use App\Contracts\WeatherContract;
use App\Http\Services\WeatherService;
use Illuminate\Support\ServiceProvider;

class WeatherProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WeatherContract::class, function ($app) {
            return new WeatherService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
