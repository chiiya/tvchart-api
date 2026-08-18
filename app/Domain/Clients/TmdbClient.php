<?php declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Jobs\UpdateTvShow;
use Carbon\CarbonImmutable;
use Chiiya\Tmdb\Repositories\BrowseRepository;
use Chiiya\Tmdb\Repositories\ChangeRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;

readonly class TmdbClient
{
    use DispatchesJobs;

    /** TMDB rejects a range longer than 14 days on the changes endpoint. */
    private const int MAX_CHANGE_WINDOW_DAYS = 14;

    public function __construct(
        private BrowseRepository $browse,
        private ChangeRepository $changes,
    ) {}

    /**
     * Import all shows since the given $season.
     */
    public function updateShowsSince(CarbonImmutable $start, int $page = 1): void
    {
        $response = $this->browse->discoverTV([
            'air_date.gte' => $start->format('Y-m-d'),
            'page' => $page,
        ]);

        foreach ($response->results as $result) {
            $this->dispatch(new UpdateTvShow($result->id));
        }

        if ($response->page < $response->total_pages) {
            $this->updateShowsSince($start, $page + 1);
        }
    }

    /**
     * Import all tv shows that changed within the given window, defaulting to
     * the last 24 hours.
     */
    public function updateChangedShows(?CarbonImmutable $start = null, ?CarbonImmutable $end = null): void
    {
        $end ??= CarbonImmutable::now();
        $start ??= $end->subDay();

        // TMDB only accepts 14 days per request, so walk longer windows in chunks.
        do {
            $chunkEnd = $start->addDays(self::MAX_CHANGE_WINDOW_DAYS)->min($end);
            $this->dispatchShowsChangedBetween($start, $chunkEnd);
            $start = $chunkEnd;
        } while ($start < $end);
    }

    /**
     * Dispatch update jobs for all shows changed within a single TMDB window.
     */
    private function dispatchShowsChangedBetween(
        CarbonImmutable $start,
        CarbonImmutable $end,
        int $page = 1,
    ): void {
        $response = $this->changes->getTvChanges([
            'page' => $page,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);

        foreach ($response->results as $result) {
            if ($result->id && ! $result->adult) {
                $this->dispatch(new UpdateTvShow($result->id));
            }
        }

        if ($response->hasMorePages()) {
            $this->dispatchShowsChangedBetween($start, $end, $page + 1);
        }
    }
}
