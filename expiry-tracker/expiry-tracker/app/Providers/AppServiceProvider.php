<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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
        Paginator::useBootstrapFive();
        View::composer('layouts.app', function ($view) {
            $count = auth()->check()
                ? Product::query()->visibleTo(auth()->user())->alerts()->whereNull('alert_dismissed_at')->where('disposal_status', 'active')->count()
                : 0;
            $view->with('navAlertCount', $count);
        });
    }
}
