<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\DTOs\Season;
use App\Domain\DTOs\UpdateTvSeasonData;
use App\Domain\Services\SeasonService;
use Carbon\CarbonImmutable;
use Chiiya\Tmdb\Entities\Television\TvSeasonDetails;
use Closure;
use DateTimeImmutable;
use Illuminate\Support\Arr;

class UpdateRecord
{
    public function __construct(
        private readonly SeasonService $seasons,
    ) {}

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
        $season = $this->getSeason($data);

        return [
            'number' => $data->season_number,
            'poster' => $data->poster_path,
            'first_air_date' => $data->air_date,
            'tvdb_id' => $data->external_ids->tvdb_id,
            'season_year' => $season?->start->year,
            'season' => $season?->name,
        ];
    }

    /**
     * Get the correct season (i.e. quarter) the tv season belongs to,
     * taking into consideration that it might overflow into the next season.
     */
    private function getSeason(TvSeasonDetails $data): ?Season
    {
        if (! $data->air_date instanceof DateTimeImmutable) {
            return null;
        }

        $date = CarbonImmutable::instance($data->air_date);
        $season = $this->seasons->getSeasonForDate($date);

        // Show doesn't start towards the end of the season
        if ($date->lt($season->end->subDays(21))) {
            return $season;
        }

        // No episodes, can't judge whether it belongs to current or next season
        if (! count($data->episodes)) {
            return $season;
        }

        $episode = $data->episodes[floor((count($data->episodes) - 1) / 2)];

        // Most episodes (>= 50%) belong to current season
        if ($episode->air_date && CarbonImmutable::instance($episode->air_date)->lt($season->end)) {
            return $season;
        }

        $lastEpisodeAirDate = $data->episodes[count($data->episodes) - 1]->air_date;

        // Most episodes (> 50%) don't belong to current season, and it ends next season
        if ($lastEpisodeAirDate && CarbonImmutable::instance($lastEpisodeAirDate)->lt($season->addSeason()->end)) {
            return $season->addSeason();
        }

        // Long-running show, might as well assign it to current season
        return $season;
    }
}
