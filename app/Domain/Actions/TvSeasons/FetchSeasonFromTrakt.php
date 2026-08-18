<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\Clients\TraktClient;
use App\Domain\DTOs\UpdateTvSeasonData;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

readonly class FetchSeasonFromTrakt
{
    public function __construct(
        private TraktClient $client,
    ) {}

    /**
     * Fetch TV season data from Trakt.
     *
     * Trakt only enriches TMDB data, so any failure here is logged and skipped
     * rather than thrown - it must never block persisting the season.
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        if (! config('tv-chart.trakt.enabled') || $data->show->imdb_id === null) {
            return $next($data);
        }

        try {
            $score = $this->client->getSeasonRating($data->show->imdb_id, $data->number);
        } catch (RequestException $exception) {
            if ($exception->response->status() !== 404) {
                Log::error('Trakt Exception', [
                    'show' => $data->show->tmdb_id,
                    'season' => $data->number,
                    'exception' => $exception,
                ]);
            }

            return $next($data);
        } catch (ConnectionException $exception) {
            Log::error('Trakt connection failure', [
                'show' => $data->show->tmdb_id,
                'season' => $data->number,
                'exception' => $exception,
            ]);

            return $next($data);
        }

        $data->trakt = ['trakt_score' => $score];

        return $next($data);
    }
}
