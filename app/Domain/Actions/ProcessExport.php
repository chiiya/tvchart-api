<?php declare(strict_types=1);

namespace App\Domain\Actions;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Enumerators\ImportResult;
use App\Domain\Repositories\ImportLogRepository;
use App\Domain\Strategies\ImportStrategy;
use Chiiya\Common\Services\FileDownloader;
use Closure;

class ProcessExport
{
    public function __construct(
        private readonly FileDownloader $downloader,
        private readonly ImportLogRepository $imports,
    ) {}

    /**
     * Import the given TMDB export into database using the given $model.
     */
    public function handle(TmdbExport $data, Closure $next): mixed
    {
        /** @var ImportStrategy $strategy */
        $strategy = resolve($data->strategy);
        $data->file = $this->downloader->download(config('tv-chart.tmdb.exports').$data->filename);
        $this->imports->startImport($data->type, $data->filename);
        $strategy->execute($data);
        $this->imports->finishImport($data->type, $data->filename, ImportResult::PROCESSED);
        $data->file?->delete();

        return $next($data);
    }
}
