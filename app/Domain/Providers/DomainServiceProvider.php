<?php declare(strict_types=1);

namespace App\Domain\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(module_path('Domain', 'Database/Migrations'));
        }

        RateLimiter::for('tmdb', fn () => Limit::perMinute(30)->by('tmdb'));
    }
}
