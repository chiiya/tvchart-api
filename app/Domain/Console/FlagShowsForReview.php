<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class FlagShowsForReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:flag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flag popular, blacklisted shows for manual review.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = TvShow::query()
            ->where('status', '=', Status::BLACKLISTED)
            ->where('flagged_for_review', '=', false)
            ->where(
                fn (Builder $builder) => $builder
                    ->where('trakt_members', '>', 5000)
                    ->orWhere('imdb_votes', '>', 5000),
            )
            ->update(['flagged_for_review' => true]);

        $this->comment("{$count} shows have been flagged for review.");

        return self::SUCCESS;
    }
}
