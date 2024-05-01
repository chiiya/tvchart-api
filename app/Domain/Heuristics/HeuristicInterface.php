<?php declare(strict_types=1);

namespace App\Domain\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;

interface HeuristicInterface
{
    public function apply(TvShow $show): ?Status;
}
