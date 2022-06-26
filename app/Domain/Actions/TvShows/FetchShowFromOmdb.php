<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\Clients\OmdbClient;
use App\Domain\DTOs\UpdateTvShowData;
use Closure;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

class FetchShowFromOmdb
{
    public function __construct(
        private OmdbClient $client,
    ) {}

    /**
     * Fetch TV show data from OMDB.
     *
     * @throws RequestException
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if ($data->imdb_id === null) {
            return $next($data);
        }

        try {
            $response = $this->client->getShow($data->imdb_id);
        } catch (RequestException $exception) {
            if ($exception->response->status() === 404) {
                return $next($data);
            }

            throw $exception;
        }

        $data->omdb = Arr::only($response, ['summary']);

        $mappings = config('tv-chart.genres.omdb');
        $genres = array_intersect(array_keys($mappings), $response['genres']);
        $data->genres = array_merge($data->genres, array_map(fn (string $genre) => $mappings[$genre], $genres));

        return $next($data);
    }
}
