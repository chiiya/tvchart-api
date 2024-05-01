<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Carbon\CarbonImmutable;

class ForeignShow implements HeuristicInterface
{
    use ChecksAvailability;

    /**
     * Blacklist a foreign show if it's only available regionally.
     */
    public function apply(TvShow $show): ?Status
    {
        $countries = $show->countries->pluck('country_code')->all();

        // Only consider non-english shows
        if ($show->primary_language === 'en' || in_array('US', $countries, true) || in_array('GB', $countries, true)) {
            return null;
        }

        if (in_array(
            $show->primary_language,
            config('tv-chart.blacklist.languages'),
            true,
        ) && ! $this->belongsToInternationallyAvailableNetwork($show)) {
            return Status::BLACKLISTED;
        }

        // Recently aired, too early to decide automatically
        if (! $show->first_air_date instanceof CarbonImmutable || $show->first_air_date->gte(now()->subMonths(12))) {
            return null;
        }

        if ($this->isAvailableInternationally($show)) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to missing international availability.');

        return Status::BLACKLISTED;
    }
}
