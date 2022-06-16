<?php declare(strict_types=1);

namespace App\Domain\Providers;

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
    }
}
