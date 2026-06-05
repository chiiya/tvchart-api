<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use App\Domain\Services\EvaluateHeuristics;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class EvaluateShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:evaluate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply the full heuristic stack to all unreviewed shows.';

    /**
     * Execute the console command.
     */
    public function handle(EvaluateHeuristics $heuristics): int
    {
        $count = 0;

        TvShow::query()
            ->where('status', '=', Status::UNREVIEWED)
            ->with(['genres', 'networks', 'countries', 'watchProviders'])
            ->chunkById(500, function (Collection $shows) use ($heuristics, &$count): void {
                foreach ($shows as $show) {
                    if ($heuristics->evaluate($show)) {
                        ++$count;
                    }
                }
            }, 'tmdb_id');

        $this->comment("{$count} shows have been updated.");

        return self::SUCCESS;
    }
}
