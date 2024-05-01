<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Filament\Resources\TvShowResource;
use App\Filament\Resources\TvShowResource\Widgets\StatusOverview;
use Filament\Resources\Pages\ListRecords;

class ListTvShows extends ListRecords
{
    protected static string $resource = TvShowResource::class;
    protected static ?string $title = 'TV Shows';

    protected function getHeaderWidgets(): array
    {
        return [StatusOverview::class];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'tmdb_id';
    }
}
