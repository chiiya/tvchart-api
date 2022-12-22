<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use App\Domain\Models\TvShow;
use Chiiya\Tmdb\Entities\Television\TvShowDetails;

class UpdateTvShowData
{
    public function __construct(
        public int $id,
        public TvShow $show,
        public ?string $imdb_id = null,
        public ?TvShowDetails $tmdb = null,
        public array $omdb = [],
        public array $trakt = [],
        public array $genres = [],
    ) {}
}
