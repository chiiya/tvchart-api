<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\ImdbClient;
use Illuminate\Console\Command;

class UpdateImdbData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:imdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch IMDB ratings and update them in database.';

    /**
     * Execute the console command.
     */
    public function handle(ImdbClient $client): int
    {
        $client->updateRatings();

        $this->comment('All ratings have been updated.');

        return self::SUCCESS;
    }
}
