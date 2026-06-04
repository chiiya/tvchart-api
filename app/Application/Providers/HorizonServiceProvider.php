<?php declare(strict_types=1);

namespace App\Application\Providers;

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define(
            'viewHorizon',
            // The unused, nullable $user parameter is load-bearing: Horizon resolves the
            // user from the default (web) guard, which is always null here. Without a
            // nullable first parameter, the Gate denies guests before invoking the callback.
            function (?FilamentUser $user = null) {
                if (! Auth::guard('filament')->check()) {
                    return false;
                }

                auth()->shouldUse('filament');
                /** @var FilamentUser $user */
                $user = Auth::guard('filament')->user();

                return $user->can('horizon.view');
            },
        );
    }
}
