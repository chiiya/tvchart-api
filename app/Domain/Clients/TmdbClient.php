<?php declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Jobs\UpdateTvShow;
use App\Domain\Services\SeasonService;
use Chiiya\Tmdb\Repositories\BrowseRepository;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TmdbClient
{
    use DispatchesJobs;

    public function __construct(
        private BrowseRepository $browse,
        private SeasonService $service,
    ) {}

    public function updateShowsFromCurrentAndLastSeason(int $page = 1): void
    {
        $currentSeason = $this->service->getCurrentSeason();

        $response = $this->browse->discoverTV([
            'air_date.gte' => $currentSeason->subSeason()->start->format('Y-m-d'),
            'air_date.lte' => $currentSeason->end->format('Y-m-d'),
            'page' => $page,
        ]);

        foreach ($response->results as $result) {
            $this->dispatch(new UpdateTvShow($result->id, $result->name));
            break;
        }

//        if ($response->page < $response->total_pages) {
//            $this->updateShowsFromCurrentAndLastSeason($page + 1);
//        }
    }
}
