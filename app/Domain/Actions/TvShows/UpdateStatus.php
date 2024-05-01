<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\BlacklistedGenre;
use App\Domain\Heuristics\BlacklistedNetwork;
use App\Domain\Heuristics\ForeignShow;
use App\Domain\Heuristics\HeuristicInterface;
use App\Domain\Heuristics\OldTvShow;
use App\Domain\Heuristics\PopularShow;
use Closure;

class UpdateStatus
{
    /**
     * Update our tv show record in database.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        if ($data->show->status !== Status::UNREVIEWED) {
            return $next($data);
        }

        $heuristics = [
            BlacklistedGenre::class,
            BlacklistedNetwork::class,
            ForeignShow::class,
            PopularShow::class,
            OldTvShow::class,
        ];

        foreach ($heuristics as $heuristicClass) {
            /** @var HeuristicInterface $heuristic */
            $heuristic = resolve($heuristicClass);
            $status = $heuristic->apply($data->show);

            if ($status instanceof Status) {
                $data->show->update(['status' => $status]);
                break;
            }
        }

        return $next($data);
    }
}
