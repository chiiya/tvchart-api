<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use Carbon\CarbonImmutable;

final class Season
{
    public function __construct(
        public string $name,
        public CarbonImmutable $start,
        public CarbonImmutable $end,
    ) {}

    public function subSeason(): self
    {
        return $this->subSeasons();
    }

    public function subSeasons(int $count = 1): self
    {
        $start = $this->start->subMonthsNoOverflow(3 * $count);

        return new self(
            name: config('tv-chart.seasons')[$start->month],
            start: $start,
            end: $start->addMonths(2)->endOfMonth(),
        );
    }

    public function addSeason(): self
    {
        return $this->addSeasons();
    }

    public function addSeasons(int $count = 1): self
    {
        $start = $this->start->addMonthsNoOverflow(3 * $count);

        return new self(
            name: config('tv-chart.seasons')[$start->month],
            start: $start,
            end: $start->addMonths(2)->endOfMonth(),
        );
    }
}
