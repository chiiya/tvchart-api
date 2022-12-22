<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Exceptions\EntityDeletedException;
use App\Domain\Exceptions\InsufficientDataException;
use App\Domain\Exceptions\ShowIsAdultException;
use Chiiya\Tmdb\Entities\Television\TvShowDetails;
use Chiiya\Tmdb\Query\AppendToResponse;
use Chiiya\Tmdb\Repositories\TvShowRepository;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

class FetchShowFromTmdb
{
    public function __construct(
        private readonly TvShowRepository $tmdb,
    ) {}

    /**
     * Fetch TV show data from TMDB.
     *
     * @throws RequestException
     * @throws EntityDeletedException
     * @throws ShowIsAdultException
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        try {
            $data->tmdb = $this->tmdb->getTvShow($data->id, [
                new AppendToResponse([
                    AppendToResponse::EXTERNAL_IDS,
                    AppendToResponse::CONTENT_RATINGS,
                    AppendToResponse::WATCH_PROVIDERS,
                ]),
            ]);
        } catch (RequestException $exception) {
            // Show was deleted from TMDB
            if ($exception->response->status() === 404) {
                if ($data->show->exists) {
                    $data->show->delete();
                }

                throw new EntityDeletedException;
            }

            throw $exception;
        }

        if ($data->tmdb->name === null) {
            throw new InsufficientDataException;
        }

        // Skip adult shows
        if ($data->tmdb->adult) {
            if ($data->show->exists) {
                $data->show->delete();
            }

            throw new ShowIsAdultException;
        }

        $data->imdb_id = $data->tmdb->external_ids->imdb_id;
        $data->genres = array_merge($data->genres, $this->getGenres($data->tmdb));

        return $next($data);
    }

    /**
     * Get a list of correct names for the TMDB genres that are of interest to us.
     */
    private function getGenres(TvShowDetails $data): array
    {
        $mappings = config('tv-chart.genres.tmdb');
        $genres = array_intersect(array_keys($mappings), Arr::pluck($data->genres, 'name'));

        return array_map(fn (string $genre) => $mappings[$genre], $genres);
    }
}
