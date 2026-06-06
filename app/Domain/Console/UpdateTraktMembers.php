<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Clients\TraktClient;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;

class UpdateTraktMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tvchart:update:trakt {--limit=3000 : Maximum number of shows to update}';

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
            ->where('status', '!=', Status::BLACKLISTED_FINAL)
            ->orderByRaw('trakt_synced_at ASC NULLS FIRST')
            ->limit((int) $this->option('limit'))
            ->get();

        foreach ($shows as $show) {
            try {
                $members = $client->getMemberCount($show->imdb_id);
            } catch (RequestException $exception) {
                if ($exception->response->status() === 404) {
                    // Unknown to Trakt, don't let it block the rotation
                    $show->update(['trakt_synced_at' => now()]);

                    continue;
                }

                Log::error('Trakt Exception', [
                    'id' => $show->tmdb_id,
                    'exception' => $exception,
                ]);

                return self::FAILURE;
            } catch (ConnectionException $exception) {
                Log::error('Trakt connection failure', [
                    'exception' => $exception,
                ]);

                return self::FAILURE;
            }

            $show->update([
                'trakt_members' => $members,
                'trakt_synced_at' => now(),
            ]);

            // Stay below the Trakt rate limit of 1000 requests per 5 minutes
            Sleep::for(400)->milliseconds();
        }

        $this->comment('All member counts have been updated.');

        return self::SUCCESS;
    }
}
