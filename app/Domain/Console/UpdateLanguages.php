<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Services\ConfigurationService;
use Chiiya\Common\Commands\TimedCommand;

class UpdateLanguages extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:languages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update available languages with data from TMDB.';

    /**
     * Execute the console command.
     */
    public function handle(ConfigurationService $service): int
    {
        $service->updateLanguages();
        $this->comment('All languages have been updated.');

        return self::SUCCESS;
    }
}
