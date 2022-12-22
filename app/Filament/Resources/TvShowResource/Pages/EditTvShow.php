<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Filament\Resources\TvShowResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTvShow extends EditRecord
{
    protected static string $resource = TvShowResource::class;

    protected function getActions(): array
    {
        return [DeleteAction::make()];
    }
}
