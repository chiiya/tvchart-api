<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Filament\Resources\TvShowResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTvShow extends CreateRecord
{
    protected static string $resource = TvShowResource::class;
}
