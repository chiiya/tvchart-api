<?php declare(strict_types=1);

namespace App\Application\Providers;

use Illuminate\Support\Str;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * @codeCoverageIgnore
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Telescope::night();

        Telescope::filter(
            fn (IncomingEntry $entry) => $entry->type !== EntryType::VIEW && ! $this->isBootEvent($entry),
        );
    }

    protected function isBootEvent(IncomingEntry $entry): bool
    {
        return $entry->type === EntryType::EVENT && Str::contains($entry->content['name'], 'boot');
    }
}
