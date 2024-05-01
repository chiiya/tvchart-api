<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TmdbClient;
use App\Domain\Services\SeasonService;
use Carbon\CarbonImmutable;
use Chiiya\Common\Commands\TimedCommand;

class ImportInitialData extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:import:initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import initial data since launch date.';

    /**
     * Execute the console command.
     */
    public function handle(SeasonService $service, TmdbClient $client): int
    {
        $service->getSeasonForDate(CarbonImmutable::create(2022, 7));
        $client->updateShowsSince($service->getCurrentSeason());

        $this->comment('All jobs have been dispatched. Make sure your queue worker is running.');

        return self::SUCCESS;
    }
}
