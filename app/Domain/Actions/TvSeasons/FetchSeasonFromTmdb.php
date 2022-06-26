<?php declare(strict_types=1);

namespace App\Domain\Actions\TvSeasons;

use App\Domain\DTOs\UpdateTvSeasonData;
use App\Domain\Exceptions\EntityDeletedException;
use Chiiya\Tmdb\Query\AppendToResponse;
use Chiiya\Tmdb\Repositories\TvSeasonRepository;
use Closure;
use Illuminate\Http\Client\RequestException;

class FetchSeasonFromTmdb
{
    public function __construct(
        private readonly TvSeasonRepository $tmdb,
    ) {}

    /**
     * Fetch TV season data from TMDB.
     *
     * @throws RequestException
     * @throws EntityDeletedException
     */
    public function handle(UpdateTvSeasonData $data, Closure $next): mixed
    {
        try {
            $data->tmdb = $this->tmdb->getTvSeason($data->show->tmdb_id, $data->number, [
                new AppendToResponse([AppendToResponse::EXTERNAL_IDS]),
            ]);
        } catch (RequestException $exception) {
            // Season was deleted from TMDB
            if ($exception->response->status() === 404) {
                $data->season->delete();

                throw new EntityDeletedException;
            }

            throw $exception;
        }

        return $next($data);
    }
}
