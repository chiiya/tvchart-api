<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\BlacklistedGenre;
use App\Domain\Heuristics\BlacklistedNetwork;
use App\Domain\Heuristics\ForeignShow;
use App\Domain\Heuristics\HeuristicInterface;
use App\Domain\Heuristics\OldTvShow;
use App\Domain\Heuristics\PopularShow;
use App\Domain\Heuristics\StaleShow;
use App\Domain\Models\TvShow;

readonly class EvaluateHeuristics
{
    /** @var list<class-string<HeuristicInterface>> */
    private const array HEURISTICS = [
        BlacklistedGenre::class,
        BlacklistedNetwork::class,
        ForeignShow::class,
        PopularShow::class,
        StaleShow::class,
        OldTvShow::class,
    ];

    /**
     * Apply the heuristic stack to an unreviewed show. The first heuristic with
     * a result wins. Returns true if the status was changed.
     */
    public function evaluate(TvShow $show): bool
    {
        foreach (self::HEURISTICS as $heuristicClass) {
            $heuristic = resolve($heuristicClass);
            $status = $heuristic->apply($show);

            if ($status instanceof Status) {
                $show->update([
                    'status' => $status,
                    'blacklist_reason' => $heuristic->reason(),
                    'status_updated_at' => now(),
                ]);

                return true;
            }
        }

        return false;
    }
}
