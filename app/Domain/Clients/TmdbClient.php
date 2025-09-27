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
     * Import all recently updated or created tv shows.
     */
    public function updateChangedShows(int $page = 1): void
    {
        $response = $this->changes->getTvChanges([
            'page' => $page,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);

        foreach ($response->results as $result) {
            if ($result->id && ! $result->adult) {
                $this->dispatch(new UpdateTvShow($result->id));
            }
        }

        if ($response->page < $response->total_pages) {
            $this->updateChangedShows($page + 1);
        }
    }
}
