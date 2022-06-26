<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Exceptions\InsufficientDataException;
use App\Domain\Models\TvShow;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FetchRecord
{
    /**
     * Fetch existing tv show record from database or create a new one.
     *
     * @throws InsufficientDataException
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if ($data->name === null) {
            throw new InsufficientDataException;
        }

        try {
            $show = TvShow::query()
                ->where('tmdb_id', '=', $data->id)
                ->with('seasons.episodes')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $show = new TvShow;
        }

        $show->fill([
            'tmdb_id' => $data->id,
            'name' => $data->name,
            'original_name' => $data->name,
        ])->save();
        $data->show = $show;

        return $next($data);
    }
}
