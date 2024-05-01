<?php declare(strict_types=1);

namespace App\Domain\Pipelines;

use App\Domain\Actions\TvShows\FetchRecord;
use App\Domain\Actions\TvShows\FetchShowFromOmdb;
use App\Domain\Actions\TvShows\FetchShowFromTmdb;
use App\Domain\Actions\TvShows\FetchShowFromTrakt;
use App\Domain\Actions\TvShows\UpdateRecord;
use App\Domain\Actions\TvShows\UpdateRelations;
use App\Domain\Actions\TvShows\UpdateSeasons;
use App\Domain\Actions\TvShows\UpdateStatus;
use App\Domain\DTOs\UpdateTvShowData;
use Illuminate\Pipeline\Pipeline;

class UpdateTvShowPipeline extends Pipeline
{
    protected $pipes = [
        FetchRecord::class,
        FetchShowFromTmdb::class,
        FetchShowFromOmdb::class,
        FetchShowFromTrakt::class,
        UpdateRecord::class,
        UpdateRelations::class,
        UpdateSeasons::class,
        UpdateStatus::class,
    ];

    /**
     * Update tv show and all related data.
     */
    public static function run(UpdateTvShowData $data): UpdateTvShowData
    {
        return app(static::class)->send($data)->thenReturn();
    }
}
