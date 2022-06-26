<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use App\Domain\Models\TvSeason;
use App\Domain\Models\TvShow;
use Chiiya\Tmdb\Entities\Television\TvSeasonDetails;
use Spatie\DataTransferObject\DataTransferObject;

class UpdateTvSeasonData extends DataTransferObject
{
    public int $id;
    public int $number;
    public TvShow $show;
    public TvSeason $season;
    public ?TvSeasonDetails $tmdb;
    public array $trakt = [];
}
