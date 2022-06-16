<?php declare(strict_types=1);

namespace App\V1\Providers;

use Illuminate\Support\ServiceProvider;

class V1ServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(module_path('V1', 'Database/Migrations'));
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
