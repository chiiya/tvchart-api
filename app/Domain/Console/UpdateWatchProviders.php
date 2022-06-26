<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Services\ConfigurationService;
use Chiiya\Common\Commands\TimedCommand;

class UpdateWatchProviders extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:providers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update available watch providers and their regions with data from TMDB.';

    /**
     * Execute the console command.
     */
    public function handle(ConfigurationService $service): int
    {
        $service->updateWatchProviders();
        $this->comment('All watch providers have been updated.');

        return self::SUCCESS;
    }
}
