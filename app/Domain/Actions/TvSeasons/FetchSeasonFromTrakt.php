<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\Clients\TraktClient;
use App\Domain\DTOs\UpdateTvSeasonData;
use Closure;
use Illuminate\Http\Client\RequestException;

class FetchSeasonFromTrakt
{
    public function __construct(
        private TraktClient $client,
    ) {}

    /**
     * Fetch TV show data from Trakt.
     *
     * @throws RequestException
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        if ($data->show->imdb_id === null) {
            return $next($data);
        }

        try {
            $score = $this->client->getSeasonRating($data->show->imdb_id, $data->number);
        } catch (RequestException $exception) {
            if ($exception->response->status() === 404) {
                return $next($data);
            }

            throw $exception;
        }

        $data->trakt = ['trakt_score' => $score];

        return $next($data);
    }
}
