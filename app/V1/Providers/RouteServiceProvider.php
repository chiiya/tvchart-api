<?php declare(strict_types=1);

namespace App\V1\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        $this->routes(function (): void {
            Route::prefix('api/v1')
                ->middleware('api')
                ->name('api.v1.')
                ->group(module_path('V1', '/Routes/api.php'));
        });
    }
}
