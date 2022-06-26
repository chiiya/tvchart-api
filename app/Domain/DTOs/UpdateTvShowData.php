<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use App\Domain\Models\TvShow;
use Chiiya\Tmdb\Entities\Television\TvShowDetails;
use Spatie\DataTransferObject\DataTransferObject;

class UpdateTvShowData extends DataTransferObject
{
    public int $id;
    public ?string $name;
    public ?string $imdb_id;
    public TvShow $show;
    public ?TvShowDetails $tmdb;
    public array $omdb = [];
    public array $trakt = [];
    public array $genres = [];
}
