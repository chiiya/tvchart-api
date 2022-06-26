<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Enumerators\ImportType;
use App\Domain\Models\Keyword;
use App\Domain\Pipelines\ImportTmdbExportPipeline;
use App\Domain\Strategies\DirectImportStrategy;
use Chiiya\Common\Commands\TimedCommand;

class UpdateKeywords extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:keywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update keywords with data from TMDB.';

    /**
     * Execute the console command.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function handle(): int
    {
        ImportTmdbExportPipeline::run(new TmdbExport(
            type: ImportType::KEYWORDS,
            filename: 'keyword_ids_'.date('m_d_Y').'.json.gz',
            strategy: DirectImportStrategy::class,
            model: new Keyword,
        ));
        $this->comment('All keywords have been updated.');

        return self::SUCCESS;
    }
}
