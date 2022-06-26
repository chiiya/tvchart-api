<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum ImportStatus: int
{
    case STARTED = 0;
    case FINISHED = 1;
}
