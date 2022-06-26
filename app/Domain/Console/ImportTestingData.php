<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TmdbClient;
use Chiiya\Common\Commands\TimedCommand;

class ImportTestingData extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:import:testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import some testing data for TV shows (all TV shows for this and last season).';

    /**
     * Execute the console command.
     */
    public function handle(TmdbClient $client): int
    {
        $client->updateShowsFromCurrentAndLastSeason();

        $this->comment('All jobs have been dispatched. Make sure your queue worker is running.');

        return self::SUCCESS;
    }
}
