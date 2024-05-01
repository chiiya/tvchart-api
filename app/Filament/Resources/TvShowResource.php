<?php declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use App\Filament\Resources\TvShowResource\Pages\ListTvShows;
use App\Filament\Resources\TvShowResource\Pages\ShowTvShow;
use App\Filament\Resources\TvShowResource\RelationManagers\TvSeasonsRelationManager;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TvShowResource extends Resource
{
    protected static ?string $model = TvShow::class;
    protected static ?string $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationLabel = 'TV Shows';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tmdb_id')->label('ID')->searchable(),
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('release_year')->label('Release Year'),
                TextColumn::make('primary_language')->label('Language'),
                BadgeColumn::make('status')
                    ->enum(Status::values())
                    ->colors([
                        'danger',
                        'secondary' => Status::UNREVIEWED->value,
                        'warning' => Status::UNDECIDED->value,
                        'success' => Status::WHITELISTED->value,
                    ]),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Status::values())
                    ->label(__('Status')),
                Filter::make('flagged_for_review')
                    ->label(__('Flagged for Review'))
                    ->query(fn (Builder $query): Builder => $query->where('flagged_for_review', '=', true)),
            ])
            ->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [TvSeasonsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTvShows::route('/'),
            'view' => ShowTvShow::route('/{record}/show'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('TV Show');
    }

    public static function getPluralModelLabel(): string
    {
        return __('TV Shows');
    }

    protected static function getNavigationGroup(): string
    {
        return __('Television');
    }
}
