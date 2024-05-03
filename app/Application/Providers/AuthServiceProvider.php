<?php declare(strict_types=1);

namespace App\Application\Providers;

use App\Domain\Models\TvShow;
use App\Domain\Policies\TvShowPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        TvShow::class => TvShowPolicy::class,
    ];

    /**
     * Boot the service provider. Register gates and policies.
     */
    public function boot(): void {}
}
