<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\RelationManagers;

use App\Domain\Models\TvSeason;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TvSeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';
    protected static ?string $recordTitleAttribute = 'number';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderByDesc('number'))
            ->columns([
                TextColumn::make('number')->label(__('Number')),
                TextColumn::make('name')->label(__('Name')),
                TextColumn::make('first_air_date')
                    ->label(__('First Air Date'))
                    ->date('Y-m-d'),
                TextColumn::make('year')
                    ->label(__('Year'))
                    ->getStateUsing(fn (TvSeason $record) => $record->getYear()),
                TextColumn::make('month')
                    ->label(__('Month'))
                    ->getStateUsing(fn (TvSeason $record) => $record->getMonthName()),
            ]);
    }
}
