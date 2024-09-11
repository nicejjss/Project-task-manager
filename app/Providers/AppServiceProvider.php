<?php

namespace App\Providers;

use App\Custom\Auth\CustomGuard;
use App\Custom\Auth\CustomProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
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
        Auth::extend('custom', function (Application $app, string $name, array $config) {
            return new CustomGuard(Auth::createUserProvider($config['provider']));
        });

        Auth::provider('custom', function (Application $app, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new CustomProvider($config['model']);
        });
    }
}
