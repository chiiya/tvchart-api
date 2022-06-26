<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Enumerators\ImportType;
use App\Domain\Models\Company;
use App\Domain\Pipelines\ImportTmdbExportPipeline;
use App\Domain\Strategies\DirectImportStrategy;
use Chiiya\Common\Commands\TimedCommand;

class UpdateCompanies extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update production companies with data from TMDB.';

    /**
     * Execute the console command.
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function handle(): int
    {
        ImportTmdbExportPipeline::run(new TmdbExport(
            type: ImportType::COMPANIES,
            filename: 'production_company_ids_'.date('m_d_Y').'.json.gz',
            strategy: DirectImportStrategy::class,
            model: new Company,
        ));
        $this->comment('All companies have been updated.');

        return self::SUCCESS;
    }
}
