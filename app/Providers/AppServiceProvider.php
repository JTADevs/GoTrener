<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\Auth;
use App\Repository\AuthInterface;
use App\Repository\User;
use App\Repository\UserInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthInterface::class, Auth::class);
        $this->app->singleton(UserInterface::class, User::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
