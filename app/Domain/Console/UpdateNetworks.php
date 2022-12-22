<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\DTOs\TmdbExport;
use App\Domain\Enumerators\ImportType;
use App\Domain\Models\Network;
use App\Domain\Pipelines\ImportTmdbExportPipeline;
use App\Domain\Strategies\DirectImportStrategy;
use Chiiya\Common\Commands\TimedCommand;

class UpdateNetworks extends TimedCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:update:networks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update networks with data from TMDB.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        ImportTmdbExportPipeline::run(new TmdbExport(
            type: ImportType::NETWORKS,
            filename: 'tv_network_ids_'.date('m_d_Y').'.json.gz',
            strategy: DirectImportStrategy::class,
            model: new Network,
        ));
        $this->comment('All networks have been updated.');

        return self::SUCCESS;
    }
}
