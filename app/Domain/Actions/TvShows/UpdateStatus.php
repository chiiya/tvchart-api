<?php declare(strict_types=1);

namespace App\Domain\Actions\TvShows;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\ChecksAvailability;
use App\Domain\Models\TvShow;
use App\Domain\Services\EvaluateHeuristics;
use Closure;

readonly class UpdateStatus
{
    use ChecksAvailability;

    public function __construct(
        private EvaluateHeuristics $heuristics,
    ) {}

    /**
     * Update the status of our tv show record in database.
     */
    public function handle(UpdateTvShowData $data, Closure $next): mixed
    {
        $show = $data->show;

        if ($show->status === Status::UNREVIEWED) {
            $this->heuristics->evaluate($show);
        } elseif ($this->shouldFlagForReview($show)) {
            $show->update(['flagged_for_review' => true]);
            activity()->on($show)->log('Flagged for review: became internationally available.');
        }

        return $next($data);
    }

    /**
     * A show previously blacklisted as unavailable has become available
     * internationally and should be reviewed again.
     */
    private function shouldFlagForReview(TvShow $show): bool
    {
        return $show->status === Status::BLACKLISTED
            && $show->blacklist_reason === BlacklistReason::UNAVAILABLE
            && ! $show->flagged_for_review
            && $this->isAvailableInternationally($show);
    }
}
