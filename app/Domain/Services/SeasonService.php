<?php declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\DTOs\Season;
use Carbon\CarbonImmutable;

class SeasonService
{
    public function getCurrentSeason(): Season
    {
        return $this->getSeasonForDate(CarbonImmutable::now());
    }

    public function getSeasonForDate(CarbonImmutable $date): Season
    {
        return match ($date->month) {
            1, 2, 3 => $this->createSeason($date, 1),
            4, 5, 6 => $this->createSeason($date, 4),
            7, 8, 9 => $this->createSeason($date, 7),
            default => $this->createSeason($date, 10),
        };
    }

    private function createSeason(CarbonImmutable $date, int $startMonth): Season
    {
        $start = CarbonImmutable::create($date->year, $startMonth);

        return new Season(
            name: config('tv-chart.seasons')[$startMonth],
            start: $start,
            end: $start->addMonths(2)->endOfMonth(),
        );
    }
}
