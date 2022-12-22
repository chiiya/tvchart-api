<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Filament\Resources\TvShowResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTvShows extends ListRecords
{
    protected static string $resource = TvShowResource::class;

    protected function getActions(): array
    {
        return [CreateAction::make()];
    }
}
