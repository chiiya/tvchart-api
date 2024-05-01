<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TraktClient;
use App\Domain\Models\TvShow;
use Illuminate\Console\Command;

class UpdateTraktMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:trakt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update count for Trakt members in database.';

    /**
     * Execute the console command.
     */
    public function handle(TraktClient $client): int
    {
        $shows = TvShow::query()
            ->whereNotNull('imdb_id')
            ->orderBy('updated_at')
            ->limit(3000)
            ->get();

        foreach ($shows as $show) {
            $show->update(['trakt_members' => $client->getMemberCount($show->imdb_id)]);
            sleep(1); // Rate limit
        }

        $this->comment('All member counts have been updated.');

        return self::SUCCESS;
    }
}
