<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum ProductionStatus: string
{
    case CONTINUING = 'continuing';
    case PLANNED = 'planned';
    case IN_PRODUCTION = 'in_production';
    case ENDED = 'ended';
    case CANCELLED = 'cancelled';
    case PILOT = 'pilot';
    case MISC = 'misc';
    public static function fromResponse(string $response): ProductionStatus
    {
        return match ($response) {
            'Returning Series' => self::CONTINUING,
            'Planned' => self::PLANNED,
            'In Production' => self::IN_PRODUCTION,
            'Ended' => self::ENDED,
            'Cancelled' => self::CANCELLED,
            'Pilot' => self::PILOT,
            default => self::MISC,
        };
    }
}
