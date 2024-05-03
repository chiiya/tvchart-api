<?php declare(strict_types=1);

namespace App\Application\Providers;

use App\Filament\Resources\TvShowResource\Widgets\StatusOverview;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as LaravelTelescopeServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class ApplicationServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Application';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        }

        $widgets = [StatusOverview::class];

        foreach ($widgets as $widget) {
            $componentName = app(ComponentRegistry::class)->getName($widget);
            Livewire::component($componentName, $widget);
        }
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(LaravelTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(HorizonServiceProvider::class);
        }
    }
}
