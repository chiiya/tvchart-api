<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvSeasonData;
use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Exceptions\EntityDeletedException;
use App\Domain\Models\TvSeason;
use App\Domain\Pipelines\UpdateTvSeasonPipeline;
use Chiiya\Tmdb\Entities\Television\TvShowDetails;
use Closure;
use Illuminate\Support\Arr;

class UpdateSeasons
{
    /**
     * Update all tv seasons and episodes.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        $this->deleteMissingSeasons($data->tmdb);

        foreach ($data->tmdb->seasons as $season) {
            try {
                UpdateTvSeasonPipeline::run(new UpdateTvSeasonData(
                    id: $season->id,
                    number: $season->season_number,
                    show: $data->show,
                    season: new TvSeason,
                ));
            } catch (EntityDeletedException) {
                // Continue
            }
        }

        return $next($data);
    }

    /**
     * Delete all tv seasons from database that are no longer returned by TMDB.
     */
    private function deleteMissingSeasons(TvShowDetails $show): void
    {
        $ids = Arr::pluck($show->seasons, 'id');
        TvSeason::query()
            ->where('tv_show_id', '=', $show->id)
            ->whereNotIn('tmdb_id', $ids)
            ->delete();
    }
}
