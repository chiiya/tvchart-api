<?php declare(strict_types=1);

namespace App\Application\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()),
        );
    }
}
