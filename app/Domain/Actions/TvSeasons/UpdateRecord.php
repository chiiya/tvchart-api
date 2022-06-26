<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\DTOs\UpdateTvSeasonData;
use Chiiya\Tmdb\Entities\Television\TvSeasonDetails;
use Closure;
use Illuminate\Support\Arr;

class UpdateRecord
{
    /**
     * Update our tv season record in database.
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        $data->season->update(array_merge(
            $this->getBaseAttributes($data->tmdb),
            $this->getCustomAttributes($data->tmdb),
            $data->trakt,
        ));

        return $next($data);
    }

    /**
     * Get base attributes from TMDB response that don't require any mapping.
     */
    private function getBaseAttributes(TvSeasonDetails $data): array
    {
        return Arr::only($data->toArray(), ['name', 'overview', 'release_year']);
    }

    /**
     * Get custom attributes from TMDB response with different naming or transformation logic.
     */
    private function getCustomAttributes(TvSeasonDetails $data): array
    {
        return [
            'number' => $data->season_number,
            'poster' => $data->poster_path,
            'first_air_date' => $data->air_date,
            'tvdb_id' => $data->external_ids->tvdb_id,
        ];
    }
}
