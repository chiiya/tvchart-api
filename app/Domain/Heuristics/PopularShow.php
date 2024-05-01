<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;

class PopularShow implements HeuristicInterface
{
    /**
     * Whitelist a show when it's popular, in English language and from a high
     * quality network.
     */
    public function apply(TvShow $show): ?Status
    {
        if ($show->imdb_votes < 10000 || $show->trakt_members < 10000) {
            return null;
        }

        if ($show->primary_language !== 'en') {
            return null;
        }

        if (! $show->networks->some(fn (Network $network) => $network->isWhitelisted())) {
            return null;
        }

        activity()->on($show)->log('Whitelisted due to popularity.');

        return Status::WHITELISTED;
    }
}
