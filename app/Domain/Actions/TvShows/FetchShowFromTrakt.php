<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\Clients\TraktClient;
use App\Domain\DTOs\UpdateTvShowData;
use Closure;
use Illuminate\Http\Client\RequestException;

class FetchShowFromTrakt
{
    public function __construct(
        private TraktClient $client,
    ) {}

    /**
     * Fetch TV show data from Trakt.
     *
     * @throws RequestException
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if ($data->imdb_id === null) {
            return $next($data);
        }

        try {
            $members = $this->client->getMemberCount($data->imdb_id);
        } catch (RequestException $exception) {
            if ($exception->response->status() === 404) {
                return $next($data);
            }

            throw $exception;
        }

        $data->trakt = ['trakt_members' => $members];

        return $next($data);
    }
}
