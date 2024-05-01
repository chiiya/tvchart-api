<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;

class BlacklistedNetwork implements HeuristicInterface
{
    /**
     * Blacklist a show if it belongs to one of the blacklisted networks.
     */
    public function apply(TvShow $show): ?Status
    {
        $blacklistedNetworks = config('tv-chart.blacklist.networks');
        $networks = $show->loadMissing('networks')->networks->pluck('name')->all();

        if (! count(array_intersect($blacklistedNetworks, $networks))) {
            return null;
        }

        activity()->on($show)->log('Blacklisted due to network.');

        return Status::BLACKLISTED;
    }
}
