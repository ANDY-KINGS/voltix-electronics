<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share low stock count across all views
        View::composer('*', function ($view) {
            $lowStockCount = 0;
            $openWarrantyClaims = 0;

            if (Schema::hasTable('products')) {
                $lowStockCount = \App\Models\Product::lowStock()->count();
            }

            if (Schema::hasTable('warranty_claims')) {
                $openWarrantyClaims = \App\Models\WarrantyClaim::where('status', 'open')->count();
            }

            $view->with('lowStockCount', $lowStockCount);
            $view->with('openWarrantyClaims', $openWarrantyClaims);
        });
    }
}
