<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvSeason;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Collection;
use Sokil\IsoCodes\IsoCodesFactory;

class TvSeasonService
{
    public function __construct(
        private readonly Repository $cache,
    ) {}

    public function getShowsForMonth(int $year, int $month): Collection
    {
        return $this->cache->remember("shows-{$year}-{$month}", now()->addDay(), function () use ($year, $month) {
            $start = CarbonImmutable::create($year, $month)->startOfMonth();
            $end = $start->endOfMonth();
            $iso = new IsoCodesFactory;
            $languages = $iso->getLanguages();
            $countries = $iso->getCountries();
            $customCountries = config('tv-chart.countries');
            $shows = TvSeason::query()
                ->join('tv_shows', 'tv_seasons.tv_show_id', '=', 'tv_shows.tmdb_id')
                ->where('tv_seasons.number', '>', 0)
                ->where('tv_shows.status', '=', Status::WHITELISTED)
                ->where('tv_seasons.first_air_date', '>=', $start)
                ->where('tv_seasons.first_air_date', '<=', $end)
                ->with([
                    'show:tmdb_id,name,runtime,imdb_id,imdb_score,trakt_members,overview,poster,primary_language',
                    'show.genres:id,name',
                    'show.countries:country_code',
                    'show.networks:tmdb_id,name',
                ])
                ->select([
                    'tv_seasons.tmdb_id',
                    'tv_seasons.tv_show_id',
                    'tv_seasons.number',
                    'tv_seasons.overview',
                    'tv_seasons.poster',
                    'tv_seasons.first_air_date',
                ])
                ->withCount('episodes')
                ->orderByDesc('tv_shows.imdb_score')
                ->get();

            return $shows->map(fn (TvSeason $season) => [
                'tv_season_id' => $season->tmdb_id,
                'tv_show_id' => $season->show->tmdb_id,
                'name' => $season->show->name,
                'number' => $season->number,
                'runtime' => $season->show->runtime,
                'imdb_id' => $season->show->imdb_id,
                'imdb_score' => $season->show->imdb_score > 0 ? (int) ($season->show->imdb_score * 10) : null,
                'trakt_members' => $season->show->trakt_members,
                'overview' => $season->overview ?? $season->show->overview,
                'poster' => $season->poster ?? $season->show->poster,
                'first_air_date' => $season->first_air_date->toIso8601String(),
                'primary_language' => $season->show->primary_language
                    ? $languages->getByAlpha2($season->show->primary_language)->getName()
                    : null,
                'episodes_count' => $season->episodes_count,
                'genres' => $season->show->genres->pluck('name')->all(),
                'networks' => $season->show->networks->pluck('name')->all(),
                'countries' => $season->show->countries
                    ->pluck('country_code')
                    ->map(fn (string $code) => $customCountries[$code] ?? $countries->getByAlpha2($code)?->getName())
                    ->filter()
                    ->values()
                    ->all(),
            ]);
        });
    }
}
