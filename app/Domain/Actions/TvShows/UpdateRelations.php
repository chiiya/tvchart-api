<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Services\CachingService;
use Chiiya\Tmdb\Entities\WatchProviders\WatchProviderList;
use Closure;
use Illuminate\Support\Collection;

class UpdateRelations
{
    public function __construct(
        private readonly CachingService $cache,
    ) {}

    /**
     * Update all tv show relations in database (except for seasons and episodes).
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        $genres = array_intersect_key($this->cache->getGenres(), array_flip($data->genres));
        $data->show->genres()->sync($genres);
        $data->show->networks()->sync(collect($data->tmdb->networks)->pluck('id')->all());
        $data->show->companies()->sync(collect($data->tmdb->production_companies)->pluck('id')->all());
        $data->show->languages()->sync($data->tmdb->languages);
        $data->show->countries()->sync(collect($data->tmdb->production_countries)->pluck('country')->all());
        $data->show->watchProviders()->detach();
        $data->show->watchProviders()->attach($this->getWatchProviderPivotEntries($data->tmdb->watch_providers));

        return $next($data);
    }

    /**
     * Get list of watch provider pivot entries to attach.
     */
    private function getWatchProviderPivotEntries(array $lists): array
    {
        $countries = $this->cache->getCountryCodes();

        return collect($lists)
            ->filter(fn (WatchProviderList $list) => isset($countries[$list->country]))
            ->map(
                fn (WatchProviderList $list) => $this->getProviderIds($list)
                    ->map(fn (int $id) => [
                        'watch_provider_id' => $id,
                        'region' => $list->country,
                    ])
                    ->all(),
            )
            ->flatten(1)
            ->all();
    }

    /**
     * Get a unique list of watch provider IDs across all channels (streaming, renting, buying).
     */
    private function getProviderIds(WatchProviderList $list): Collection
    {
        return collect(array_merge($list->flatrate ?? [], $list->rent ?? [], $list->buy ?? []))
            ->pluck('provider_id')
            ->unique();
    }
}
