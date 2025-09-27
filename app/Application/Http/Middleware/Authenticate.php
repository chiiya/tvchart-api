<?php declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * @codeCoverageIgnore
 */
class Authenticate extends Middleware
{
    /**
     * {@inheritDoc}
     */
    public function handle($request, Closure $next, ...$guards): mixed
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('admin*') || $request->is('filament*')) {
            return route('filament.auth.login');
        }

        if (! $request->expectsJson()) {
            return route('login');
        }

        return null;
    }
}
