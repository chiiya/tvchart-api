<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\DTOs\UpdateTvSeasonData;
use App\Domain\Models\TvSeason;
use Closure;

class FetchRecord
{
    /**
     * Fetch existing tv season record from database or create a new one.
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        $data->season = TvSeason::query()
            ->updateOrCreate(
                ['tmdb_id' => $data->id],
                ['number' => $data->number, 'tv_show_id' => $data->show->tmdb_id],
            )
            ->load('episodes');

        return $next($data);
    }
}
