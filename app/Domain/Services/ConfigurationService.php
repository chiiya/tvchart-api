<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\Country;
use App\Domain\Models\Language;
use App\Domain\Models\WatchProvider;
use App\Domain\Models\WatchProviderRegion;
use Chiiya\Tmdb\Entities\Configuration\Country as TmdbCountry;
use Chiiya\Tmdb\Entities\Configuration\Language as TmdbLanguage;
use Chiiya\Tmdb\Entities\WatchProviders\WatchProvider as TmdbWatchProvider;
use Chiiya\Tmdb\Repositories\ConfigurationRepository;
use Chiiya\Tmdb\Repositories\WatchProviderRepository;

class ConfigurationService
{
    public function __construct(
        private readonly ConfigurationRepository $configuration,
        private readonly WatchProviderRepository $providers,
    ) {}

    /**
     * Update countries in database with data from TMDB.
     */
    public function updateCountries(): void
    {
        $countries = $this->configuration->getCountries();
        Country::query()->upsert(collect($countries)->map(fn (TmdbCountry $country) => [
            'country_code' => $country->country,
            'name' => $country->english_name,
            'native_name' => $country->native_name,
        ])->all(), ['country_code'], ['name', 'native_name']);
    }

    /**
     * Update languages in database with data from TMDB.
     */
    public function updateLanguages(): void
    {
        $languages = $this->configuration->getLanguages();
        Language::query()->upsert(collect($languages)->map(fn (TmdbLanguage $language) => [
            'language_code' => $language->language,
            'name' => $language->english_name,
        ])->all(), ['language_code'], ['name']);
    }

    /**
     * Update watch providers in database with data from TMDB.
     */
    public function updateWatchProviders(): void
    {
        $regions = $this->providers->getAvailableRegions();
        WatchProviderRegion::query()->upsert(collect($regions)->map(fn (TmdbCountry $country) => [
            'country' => $country->country,
            'native_name' => $country->native_name,
        ])->all(), ['country'], ['native_name']);

        $providers = $this->providers->getTvProviders();
        WatchProvider::query()->upsert(collect($providers)->map(fn (TmdbWatchProvider $provider) => [
            'tmdb_id' => $provider->provider_id,
            'name' => $provider->provider_name,
            'logo' => $provider->logo_path,
            'priority' => $provider->display_priority,
        ])->all(), ['tmdb_id'], ['name', 'logo', 'priority']);
    }
}
