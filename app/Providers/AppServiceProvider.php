<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repository\Auth;
use App\Repository\AuthInterface;
use App\Repository\Chat;
use App\Repository\ChatInterface;
use App\Repository\Trainer;
use App\Repository\TrainerInterface;
use App\Repository\User;
use App\Repository\UserInterface;
use App\Repository\Payment;
use App\Repository\PaymentInterface;
use App\Repository\Profile\Calendar;
use App\Repository\Profile\CalendarInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthInterface::class, Auth::class);
        $this->app->singleton(UserInterface::class, User::class);
        $this->app->singleton(TrainerInterface::class, Trainer::class);
        $this->app->singleton(ChatInterface::class, Chat::class);
        $this->app->singleton(PaymentInterface::class, Payment::class);
        $this->app->singleton(CalendarInterface::class, Calendar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
