<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Models\TvShow;
use App\Domain\Models\WatchProvider;
use Illuminate\Database\Eloquent\Collection;

trait ChecksAvailability
{
    /**
     * Is the show available on international networks or streaming services?
     */
    private function isAvailableInternationally(TvShow $show): bool
    {
        return $this->belongsToInternationallyAvailableNetwork($show)
            || $this->isStreamingOnInternationallyAvailableProvider($show);
    }

    /**
     * Belongs to one of the internationally available networks, meanings it's most likely streaming
     * outside its origin country.
     */
    private function belongsToInternationallyAvailableNetwork(TvShow $show): bool
    {
        $networks = $show->networks->pluck('name')->all();

        return (bool) (array_intersect($networks, config('tv-chart.international_networks')));
    }

    /**
     * Is explicitly being streamed on one of the major, internationally available providers.
     */
    private function isStreamingOnInternationallyAvailableProvider(TvShow $show): bool
    {
        $show->loadMissing('watchProviders');

        $usWatchProviders = $show->watchProviders->where('pivot.region', '=', 'US');
        // Germany as the largest mainland european streaming market
        $deWatchProviders = $show->watchProviders->where('pivot.region', '=', 'DE');

        // For these languages, there are niche streaming providers, we only care about the big ones
        if (in_array($show->primary_language, ['zh', 'ko'], true)) {
            return $this->isStreamingOnMajorProvider($usWatchProviders)
                || $this->isStreamingOnMajorProvider($deWatchProviders);
        }

        return $usWatchProviders->count() > 0 || $deWatchProviders->count() > 0;
    }

    /**
     * @param Collection<int, WatchProvider> $providers
     *
     * @noinspection PhpDocSignatureIsNotCompleteInspection
     */
    private function isStreamingOnMajorProvider(Collection $providers): bool
    {
        foreach ($providers as $provider) {
            if ($provider->isWhitelisted()) {
                return true;
            }
        }

        return false;
    }
}
