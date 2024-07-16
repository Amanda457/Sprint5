<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
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
        Passport::loadKeysFrom(storage_path('oauth'));
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Gate::define('is-admin', function ($user) {
            return $user->role === 'admin';
        });
    
        Gate::define('is-player', function ($user) {
            return $user->role === 'player';
        });
    }
}
