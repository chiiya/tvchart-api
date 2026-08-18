<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;

class PopularShow implements HeuristicInterface
{
    /**
     * Whitelist a show when it's popular, in English language and from a high
     * quality network.
     *
     * Popularity rests on IMDB votes alone since Trakt member counts are no
     * longer being fetched, so requiring them would never whitelist anything.
     */
    public function apply(TvShow $show): ?Status
    {
        if ($show->imdb_votes < 10000) {
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

    public function reason(): ?BlacklistReason
    {
        return null;
    }
}
