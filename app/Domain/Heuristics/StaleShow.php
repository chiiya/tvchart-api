<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Carbon\CarbonImmutable;

class StaleShow implements HeuristicInterface
{
    /**
     * Blacklist a show that aired a while ago but never gained any traction.
     */
    public function apply(TvShow $show): ?Status
    {
        $cutoff = now()->subMonths((int) config('tv-chart.stale.aired_before_months'));

        if (! $show->first_air_date instanceof CarbonImmutable || $show->first_air_date->gte($cutoff)) {
            return null;
        }

        if ($show->imdb_votes >= (int) config('tv-chart.stale.max_imdb_votes')
            || $show->trakt_members >= (int) config('tv-chart.stale.max_trakt_members')) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to lack of traction.');

        return Status::BLACKLISTED;
    }

    public function reason(): ?BlacklistReason
    {
        return BlacklistReason::NO_TRACTION;
    }
}
