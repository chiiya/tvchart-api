<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\Clients\TraktClient;
use App\Domain\DTOs\UpdateTvShowData;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

readonly class FetchShowFromTrakt
{
    public function __construct(
        private TraktClient $client,
    ) {}

    /**
     * Fetch TV show data from Trakt.
     *
     * Trakt only enriches TMDB data, so any failure here is logged and skipped
     * rather than thrown - it must never block persisting the show.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if (! config('tv-chart.trakt.enabled') || $data->imdb_id === null) {
            return $next($data);
        }

        try {
            $members = $this->client->getMemberCount($data->imdb_id);
            $show = $this->client->getShowSummary($data->imdb_id);
        } catch (RequestException $exception) {
            if ($exception->response->status() !== 404) {
                Log::error('Trakt Exception', [
                    'id' => $data->id,
                    'exception' => $exception,
                ]);
            }

            return $next($data);
        } catch (ConnectionException $exception) {
            Log::error('Trakt connection failure', [
                'id' => $data->id,
                'exception' => $exception,
            ]);

            return $next($data);
        }

        $data->trakt = [
            'trakt_members' => $members,
            'trakt_synced_at' => now(),
            'runtime' => $show['runtime'],
        ];

        $mappings = config('tv-chart.genres.trakt');
        $genres = array_intersect(array_keys($mappings), $show['genres']);
        $data->genres = array_merge($data->genres, array_map(fn (string $genre) => $mappings[$genre], $genres));

        return $next($data);
    }
}
