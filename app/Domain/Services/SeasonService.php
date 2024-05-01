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

    public function getSeason(int $year, string $name): Season
    {
        return match ($name) {
            'winter' => $this->createSeason($year, 1),
            'spring' => $this->createSeason($year, 4),
            'summer' => $this->createSeason($year, 7),
            'fall' => $this->createSeason($year, 10),
        };
    }

    public function getSeasonForDate(CarbonImmutable $date): Season
    {
        return match ($date->month) {
            1, 2, 3 => $this->createSeason($date->year, 1),
            4, 5, 6 => $this->createSeason($date->year, 4),
            7, 8, 9 => $this->createSeason($date->year, 7),
            default => $this->createSeason($date->year, 10),
        };
    }

    private function createSeason(int $year, int $startMonth): Season
    {
        $start = CarbonImmutable::create($year, $startMonth);

        return new Season(
            name: config('tv-chart.seasons')[$startMonth],
            start: $start,
            end: $start->addMonths(2)->endOfMonth(),
        );
    }
}
