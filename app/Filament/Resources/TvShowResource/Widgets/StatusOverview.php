<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Widgets;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = TvShow::query()
            ->groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status')
            ->all();
        $flagged = TvShow::query()->where('flagged_for_review', '=', true)->count();
        $unreviewed = TvShow::query()
            ->where('status', '=', Status::UNREVIEWED)
            ->whereNotNull('poster')
            ->whereNotNull('overview')
            ->whereNotNull('first_air_date')
            ->count();
        $total = TvShow::query()->count();

        return [
            Stat::make('Unreviewed', $unreviewed),
            Stat::make('Whitelisted', ($stats[Status::WHITELISTED->value] ?? 0).'/'.$total),
            Stat::make('Undecided', $stats[Status::UNDECIDED->value] ?? 0),
            Stat::make('Flagged for Review', $flagged),
        ];
    }
}
