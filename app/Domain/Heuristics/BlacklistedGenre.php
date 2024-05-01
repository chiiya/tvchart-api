<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;

class BlacklistedGenre implements HeuristicInterface
{
    use ChecksAvailability;

    /**
     * Blacklist a show if it belongs to one of the blacklisted genres.
     */
    public function apply(TvShow $show): ?Status
    {
        $blacklistedGenres = config('tv-chart.blacklist.genres');
        $genres = $show->loadMissing('genres')->genres->pluck('name')->all();

        // Stricter requirements for documentaries
        if (in_array('Documentary', $genres, true) && ! $this->isAvailableInternationally($show)) {
            activity()->on($show)->log('Blacklisted documentary due to missing international availability.');

            return Status::BLACKLISTED_FINAL;
        }

        // Some genres are blacklisted entirely
        if (! count(array_intersect($blacklistedGenres, $genres))) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to genres.');

        return Status::BLACKLISTED_FINAL;
    }
}
