<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TmdbClient;
use Carbon\CarbonImmutable;
use Chiiya\Common\Commands\TimedCommand;

class ImportShowsSince extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tvchart:import:since {date : Import all shows with episodes airing since this date (Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all shows airing since the given date, e.g. to backfill an import gap.';

    /**
     * Execute the console command.
     */
    public function handle(TmdbClient $client): int
    {
        $client->updateShowsSince(CarbonImmutable::parse($this->argument('date')));

        $this->comment('All jobs have been dispatched. Make sure your queue worker is running.');

        return self::SUCCESS;
    }
}
