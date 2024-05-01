<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\RelationManagers;

use App\Domain\Models\TvSeason;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class TvSeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';
    protected static ?string $recordTitleAttribute = 'number';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number'),
                TextColumn::make('name'),
                TextColumn::make('first_air_date')->date('Y-m-d'),
                TextColumn::make('season')
                    ->getStateUsing(fn (TvSeason $record) => $record->season_year.' - '.ucfirst($record->season ?? '')),
            ]);
    }

    public function getRelationship(): Relation|Builder
    {
        return $this->getOwnerRecord()->seasons()->orderByDesc('number');
    }
}
