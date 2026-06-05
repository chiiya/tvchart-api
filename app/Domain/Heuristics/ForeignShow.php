<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Actions\TvShows\UpdateStatus;
use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;

class ForeignShow implements HeuristicInterface
{
    use ChecksAvailability;

    /**
     * Blacklist a foreign show if it's only available regionally. If it becomes
     * available internationally later, it is flagged for review again.
     *
     * @see UpdateStatus
     */
    public function apply(TvShow $show): ?Status
    {
        $countries = $show->countries->pluck('country_code')->all();

        // Only consider non-english shows
        if ($show->primary_language === 'en' || in_array('US', $countries, true) || in_array('GB', $countries, true)) {
            return null;
        }

        if ($this->isAvailableInternationally($show)) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to missing international availability.');

        return Status::BLACKLISTED;
    }

    public function reason(): ?BlacklistReason
    {
        return BlacklistReason::UNAVAILABLE;
    }
}
