<?php declare(strict_types=1);

namespace App\Domain\Actions;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Exceptions\ImportAlreadyProcessedException;
use App\Domain\Repositories\ImportLogRepository;
use Closure;

class PreventDuplicateProcessing
{
    public function __construct(
        private readonly ImportLogRepository $imports,
    ) {}

    /**
     * Filter out those files that have already been imported previously.
     */
    public function handle(TmdbExport $data, Closure $next): mixed
    {
        if ($this->imports->fileHasAlreadyBeenProcessed($data->filename)) {
            throw new ImportAlreadyProcessedException;
        }

        return $next($data);
    }
}
