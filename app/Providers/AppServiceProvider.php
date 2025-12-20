<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
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
        // Configure base path for subdirectory hosting
        $basePath = config('app.base_path');
        if (!empty($basePath)) {
            URL::forceRootUrl(config('app.url') . $basePath);
        }

        Vite::prefetch(concurrency: 3);
    }
}
