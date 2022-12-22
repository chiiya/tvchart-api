<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Enumerators\ProductionStatus;
use Chiiya\Tmdb\Entities\Television\TvShowDetails;
use Closure;
use Illuminate\Support\Arr;

class UpdateRecord
{
    /**
     * Update our tv show record in database.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if (! $data->show->exists) {
            $data->show->fill([
                'tmdb_id' => $data->id,
            ]);
        }

        $data->show->fill(array_merge(
            $this->getBaseAttributes($data->tmdb),
            $this->getCustomAttributes($data->tmdb, $data->trakt),
            $data->omdb,
            Arr::except($data->trakt, ['runtime']),
        ));
        $data->show->save();

        return $next($data);
    }

    /**
     * Get base attributes from TMDB response that don't require any mapping.
     */
    private function getBaseAttributes(TvShowDetails $data): array
    {
        return Arr::only($data->toArray(), [
            'name',
            'original_name',
            'first_air_date',
            'release_year',
            'overview',
            'type',
            'popularity',
        ]);
    }

    /**
     * Get custom attributes from TMDB response with different naming or transformation logic.
     */
    private function getCustomAttributes(TvShowDetails $data, array $trakt): array
    {
        return [
            'runtime' => $data->episode_run_time[0] ?? $trakt['runtime'] ?? null,
            'backdrop' => $data->backdrop_path,
            'poster' => $data->poster_path,
            'production_status' => ProductionStatus::fromResponse($data->status),
            'primary_language' => $data->original_language,
            'content_rating' => collect($data->content_ratings)->firstWhere('country', '=', 'US')?->rating,
            'imdb_id' => $data->external_ids->imdb_id,
            'tvdb_id' => $data->external_ids->tvdb_id,
        ];
    }
}
