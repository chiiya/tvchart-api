<?php declare(strict_types=1);

namespace App\Domain\Actions;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Exceptions\ImportAlreadyProcessedException;
use App\Domain\Repositories\ImportLogRepository;
use Closure;

readonly class PreventDuplicateProcessing
{
    public function __construct(
        private ImportLogRepository $imports,
    ) {}

    /**
     * Filter out those files that have already been imported previously.
     *
     * @throws ImportAlreadyProcessedException
     */
    public function handle(TmdbExport $data, Closure $next): mixed
    {
        if ($this->imports->fileHasAlreadyBeenProcessed($data->filename)) {
            throw new ImportAlreadyProcessedException;
        }

        return $next($data);
    }
}
