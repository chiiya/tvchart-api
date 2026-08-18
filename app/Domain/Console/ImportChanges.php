<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TmdbClient;
use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;
use Chiiya\Common\Commands\TimedCommand;

class ImportChanges extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tvchart:import:changes
        {--start= : First day to import changes for (Y-m-d), defaults to yesterday}
        {--end= : Last day to import changes for (Y-m-d), defaults to today}';

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
        try {
            $start = $this->parseOption('start');
            $end = $this->parseOption('end');
        } catch (InvalidFormatException) {
            $this->log('Could not parse the given dates. Use the Y-m-d format.');

            return self::FAILURE;
        }

        if ($start instanceof CarbonImmutable && $end instanceof CarbonImmutable && $start->gt($end)) {
            $this->log('The start date must not be after the end date.');

            return self::FAILURE;
        }

        $client->updateChangedShows($start, $end);

        $this->comment('All jobs have been dispatched. Make sure your queue worker is running.');

        return self::SUCCESS;
    }

    /**
     * Parse a date option, if it was given.
     *
     * @throws InvalidFormatException
     */
    private function parseOption(string $name): ?CarbonImmutable
    {
        $value = $this->option($name);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return CarbonImmutable::parse($value)->startOfDay();
    }
}
