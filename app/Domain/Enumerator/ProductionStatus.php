<?php declare(strict_types=1);

namespace App\Domain\Enumerator;

enum ProductionStatus: string
{
    case CONTINUING = 'continuing';
    case PLANNED = 'planned';
    case IN_PRODUCTION = 'in_production';
    case ENDED = 'ended';
    case CANCELLED = 'cancelled';
    case PILOT = 'pilot';
}
