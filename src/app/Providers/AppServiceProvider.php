<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
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
        DB::listen(function ($query) {
            // Log query SQL
            Log::info($query->sql);
            // Log data yang diikat (bindings)
            Log::info($query->bindings);
        });

        View::composer('*', function ($view) {
        if (Auth::check()) {
            $view->with('notifications', Auth::user()->notifications);
            $view->with('unread', Auth::user()->unreadNotifications);
        } else {
            $view->with('notifications', collect([]));
            $view->with('unread', collect([]));
        }
    });
    }
}
