<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Models\TvShow;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FetchRecord
{
    /**
     * Fetch existing tv show record from database or create a new one.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        try {
            $show = TvShow::query()
                ->where('tmdb_id', '=', $data->id)
                ->with('seasons.episodes')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $show = new TvShow;
        }

        $data->show = $show;

        return $next($data);
    }
}
