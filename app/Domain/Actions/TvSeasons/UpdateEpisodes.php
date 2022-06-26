<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\DTOs\UpdateTvSeasonData;
use App\Domain\Models\TvEpisode;
use Chiiya\Tmdb\Entities\Television\TvSeasonDetails;
use Closure;
use Illuminate\Support\Arr;

class UpdateEpisodes
{
    /**
     * Update our tv episode records in database.
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        $this->deleteMissingEpisodes($data->tmdb);

        foreach ($data->tmdb->episodes as $attributes) {
            $episode = $this->fetchOrCreateEpisode($data, $attributes->id);
            $episode->fill([
                'name' => $attributes->name,
                'number' => $attributes->episode_number,
                'first_air_date' => $attributes->air_date,
                'overview' => $attributes->overview,
                'runtime' => $attributes->runtime,
                'still' => $attributes->still_path,
            ])->save();
        }

        return $next($data);
    }

    /**
     * Fetch episode from database or create a new one if missing.
     */
    private function fetchOrCreateEpisode(UpdateTvSeasonData $data, int $id): TvEpisode
    {
        if ($episode = $data->season->episodes->firstWhere('tmdb_id', '=', $id)) {
            return $episode;
        }

        return new TvEpisode([
            'tmdb_id' => $id,
            'tv_season_id' => $data->season->tmdb_id,
        ]);
    }

    /**
     * Delete all tv episodes from database that are no longer returned by TMDB.
     */
    private function deleteMissingEpisodes(TvSeasonDetails $season): void
    {
        $ids = Arr::pluck($season->episodes, 'id');
        TvEpisode::query()
            ->where('tv_season_id', '=', $season->id)
            ->whereNotIn('tmdb_id', $ids)
            ->delete();
    }
}
