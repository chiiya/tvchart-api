<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Services\ConfigurationService;
use Chiiya\Common\Commands\TimedCommand;

class UpdateCountries extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update available countries with data from TMDB.';

    /**
     * Execute the console command.
     */
    public function handle(ConfigurationService $service): int
    {
        $service->updateCountries();
        $this->comment('All countries have been updated.');

        return self::SUCCESS;
    }
}
