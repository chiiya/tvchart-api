<?php declare(strict_types=1);

namespace App\Domain\Pipelines;

use App\Domain\Actions\TvSeasons\FetchRecord;
use App\Domain\Actions\TvSeasons\FetchSeasonFromTmdb;
use App\Domain\Actions\TvSeasons\FetchSeasonFromTrakt;
use App\Domain\Actions\TvSeasons\UpdateEpisodes;
use App\Domain\Actions\TvSeasons\UpdateRecord;
use App\Domain\DTOs\UpdateTvSeasonData;
use Illuminate\Pipeline\Pipeline;

class UpdateTvSeasonPipeline extends Pipeline
{
    protected $pipes = [
        FetchRecord::class,
        FetchSeasonFromTmdb::class,
        FetchSeasonFromTrakt::class,
        UpdateRecord::class,
        UpdateEpisodes::class,
    ];

    /**
     * Update tv season and all related data.
     */
    public static function run(UpdateTvSeasonData $data): UpdateTvSeasonData
    {
        return app(static::class)->send($data)->thenReturn();
    }
}
