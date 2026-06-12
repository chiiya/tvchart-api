<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Genre;
use Illuminate\Contracts\Cache\Repository;

readonly class FacetService
{
    public function __construct(
        private Repository $cache,
    ) {}

    /**
     * Stable filter vocabularies for the frontend: all curated genres (i.e.
     * excluding blacklisted ones) and a short, curated list of major networks.
     *
     * @return array{genres: array<int, string>, networks: array<int, string>}
     */
    public function facets(): array
    {
        return $this->cache->remember('facets', now()->addDay(), fn () => [
            'genres' => $this->genres(),
            'networks' => $this->networks(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function genres(): array
    {
        return Genre::query()
            ->whereNotIn('name', config('tv-chart.blacklist.genres'))
            ->orderBy('name')
            ->get(['name'])
            ->map(fn (Genre $genre): string => $genre->name)
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function networks(): array
    {
        return config('tv-chart.facets.networks');
    }
}
