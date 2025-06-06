<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvEpisode;
use App\Domain\Models\TvSeason;
use App\Domain\Models\TvShow;
use Carbon\CarbonImmutable;

class OldTvShow implements HeuristicInterface
{
    /**
     * Blacklist a show if its latest air date is before our supported start date.
     */
    public function apply(TvShow $show): ?Status
    {
        /** @var TvSeason|null $latestSeason */
        $latestSeason = $show->seasons()
            ->whereNotNull('first_air_date')
            ->orderByDesc('first_air_date')
            ->first();
        /** @var TvEpisode|null $latestEpisode */
        $latestEpisode = $latestSeason?->episodes()
            ->whereNotNull('first_air_date')
            ->orderByDesc('first_air_date')
            ->first();

        $start = CarbonImmutable::createFromDate(2010);

        // No air date, or airing after the supported start date
        if (! $show->first_air_date instanceof CarbonImmutable
            || $show->first_air_date->gte($start)
            || $latestSeason?->first_air_date?->gte($start)
            || $latestEpisode?->first_air_date?->gte($start)) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to release date.');

        return Status::BLACKLISTED;
    }

    public function reason(): ?BlacklistReason
    {
        return BlacklistReason::OLD;
    }
}
