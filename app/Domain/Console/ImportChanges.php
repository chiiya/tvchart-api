<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TmdbClient;
use Chiiya\Common\Commands\TimedCommand;

class ImportChanges extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:import:changes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all recently updated or created tv shows.';

    /**
     * Execute the console command.
     */
    public function handle(TmdbClient $client): int
    {
        $client->updateChangedShows();

        $this->comment('All jobs have been dispatched. Make sure your queue worker is running.');

        return self::SUCCESS;
    }
}
