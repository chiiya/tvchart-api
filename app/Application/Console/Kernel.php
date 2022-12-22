<?php declare(strict_types=1);

namespace App\Application\Console;

use App\Domain\Console\ImportChanges;
use App\Domain\Console\UpdateCompanies;
use App\Domain\Console\UpdateCountries;
use App\Domain\Console\UpdateLanguages;
use App\Domain\Console\UpdateNetworks;
use App\Domain\Console\UpdateWatchProviders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @codeCoverageIgnore
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(UpdateCompanies::class)->daily();
        $schedule->command(UpdateCountries::class)->daily();
        $schedule->command(UpdateLanguages::class)->daily();
        $schedule->command(UpdateNetworks::class)->daily();
        $schedule->command(UpdateWatchProviders::class)->daily();
        $schedule->command(ImportChanges::class)->dailyAt('00:15');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        $this->load(module_path('Domain', 'Console'));
    }
}
