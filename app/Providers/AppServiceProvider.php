<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.dashboard', function (ViewInstance $view): void {
            $user = auth()->user();

            if ($user && ! array_key_exists('notificationCount', $view->getData())) {
                $view->with(
                    'notificationCount',
                    $user->notifications()->available()->whereNull('read_at')->count()
                );
            }
        });
    }
}