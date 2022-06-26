<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Models\Genre;
use App\Domain\Services\CachingService;
use Closure;

class UpdateGenres
{
    public function __construct(
        private CachingService $cache,
    ) {}

    /**
     * Update all tv show relations in database (except for seasons and episodes).
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        $genres = $this->cache->getGenres()->filter(fn (Genre $genre) => in_array($genre->name, $data->genres, true));
        $data->show->genres()->sync($genres->pluck('id')->all());

        return $next($data);
    }
}
