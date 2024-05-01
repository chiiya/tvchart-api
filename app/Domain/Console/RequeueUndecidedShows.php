<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Console\Command;

class RequeueUndecidedShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:requeue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Requeue all shows that have been marked as undecided.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = TvShow::query()
            ->where('status', '=', Status::UNDECIDED)
            ->where('first_air_date', '<=', now())
            ->update(['status' => Status::UNREVIEWED]);

        $this->comment("{$count} shows have been requeued.");

        return self::SUCCESS;
    }
}
