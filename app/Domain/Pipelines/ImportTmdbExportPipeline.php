<?php declare(strict_types=1);

namespace App\Domain\Pipelines;

use App\Domain\Actions\DeleteMissingEntries;
use App\Domain\Actions\PreventDuplicateProcessing;
use App\Domain\Actions\ProcessExport;
use App\Domain\DTOs\TmdbExport;
use Illuminate\Pipeline\Pipeline;

class ImportTmdbExportPipeline extends Pipeline
{
    protected $pipes = [PreventDuplicateProcessing::class, ProcessExport::class, DeleteMissingEntries::class];

    /**
     * Import data from a TMDB export file.
     */
    public static function run(TmdbExport $data): TmdbExport
    {
        return app(static::class)->send($data)->thenReturn();
    }
}
