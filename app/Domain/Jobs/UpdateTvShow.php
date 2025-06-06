<?php declare(strict_types=1);

namespace App\Domain\Jobs;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Exceptions\EntityDeletedException;
use App\Domain\Exceptions\InsufficientDataException;
use App\Domain\Exceptions\ShowIsAdultException;
use App\Domain\Models\TvShow;
use App\Domain\Pipelines\UpdateTvShowPipeline;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateTvShow implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $id,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            UpdateTvShowPipeline::run(new UpdateTvShowData(id: $this->id, show: new TvShow));
        } catch (EntityDeletedException | InsufficientDataException | ShowIsAdultException) {
            // Skip this show
        } catch (Exception $exception) {
            Log::error('Failed to update show', ['id' => $this->id, 'exception' => $exception]);

            throw $exception;
        }
    }
//
//    public function middleware(): array
//    {
//        return [new RateLimitedWithRedis('tmdb')];
//    }
}
