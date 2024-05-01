<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Country;
use App\Domain\Models\Genre;
use App\Domain\Models\Language;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Collection;

class CachingService
{
    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    public function getGenres(): array
    {
        return $this->cache->remember(
            'genres',
            now()->addDay(),
            fn () => Genre::query()->get()->pluck('id', 'name')->all(),
        );
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->cache->remember('languages', now()->addDay(), fn () => Language::query()->get());
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
