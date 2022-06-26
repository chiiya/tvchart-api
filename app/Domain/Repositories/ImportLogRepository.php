<?php declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Enumerators\ImportResult;
use App\Domain\Enumerators\ImportStatus;
use App\Domain\Enumerators\ImportType;
use App\Domain\Models\ImportLog;
use Chiiya\Common\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends AbstractRepository<ImportLog>
 */
class ImportLogRepository extends AbstractRepository
{
    protected string $model = ImportLog::class;

    /**
     * For the given $filename, check if it has already been processed.
     */
    public function fileHasAlreadyBeenProcessed(string $filename): bool
    {
        return $this->newQuery()
            ->where('filename', '=', $filename)
            ->where('status', '=', ImportStatus::FINISHED)
            ->exists();
    }

    /**
     * Start a new import (creates entry in logs table).
     */
    public function startImport(ImportType $type, string $filename): void
    {
        $this->create([
            'import_type' => $type,
            'filename' => $filename,
            'status' => ImportStatus::STARTED,
        ]);
    }

    /**
     * Finish an import with the given $result (creates entry in logs table).
     */
    public function finishImport(ImportType $type, string $filename, ImportResult $result): void
    {
        $this->create([
            'import_type' => $type,
            'filename' => $filename,
            'status' => ImportStatus::FINISHED,
            'result' => $result,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function applyFilters(Builder $builder, array $parameters): Builder
    {
        return $builder;
    }
}
