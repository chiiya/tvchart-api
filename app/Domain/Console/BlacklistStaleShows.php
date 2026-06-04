<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\StaleShow;
use App\Domain\Models\TvShow;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class BlacklistStaleShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:blacklist-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Blacklist all unreviewed shows that aired a while ago without gaining traction.';

    /**
     * Execute the console command.
     */
    public function handle(StaleShow $heuristic): int
    {
        $count = 0;

        TvShow::query()
            ->where('status', '=', Status::UNREVIEWED)
            ->chunkById(500, function (Collection $shows) use ($heuristic, &$count): void {
                foreach ($shows as $show) {
                    if ($heuristic->apply($show) instanceof Status) {
                        $show->update([
                            'status' => Status::BLACKLISTED,
                            'blacklist_reason' => $heuristic->reason(),
                            'status_updated_at' => now(),
                        ]);
                        ++$count;
                    }
                }
            }, 'tmdb_id');

        $this->comment("{$count} stale shows have been blacklisted.");

        return self::SUCCESS;
    }
}
