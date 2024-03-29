<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Country;
use App\Domain\Models\Genre;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Collection;

class CachingService
{
    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->cache->remember('genres', now()->addDay(), fn () => Genre::query()->get());
    }

    public function getCountryCodes(): array
    {
        return $this->cache->remember(
            'country-codes',
            now()->addDay(),
            fn () => Country::query()
                ->get()
                ->pluck('country_code', 'country_code')
                ->all(),
        );
    }
}
