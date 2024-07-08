<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
    public function boot()
    {
        $this->register();

        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });
    
        Gate::define('isUser', function ($user) {
            return $user->role === 'users';
        });    
    }
}
