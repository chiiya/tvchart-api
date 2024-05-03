<?php declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Enumerators\Status;
use App\Domain\Models\Language;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use App\Domain\Models\WatchProvider;
use App\Filament\Resources\TvShowResource\Pages\ListTvShows;
use App\Filament\Resources\TvShowResource\Pages\ShowTvShow;
use App\Filament\Resources\TvShowResource\RelationManagers\TvSeasonsRelationManager;
use Carbon\CarbonImmutable;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

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
                TextColumn::make('release_year')->label('Release Year')->sortable(),
                TextColumn::make('primary_language')->label('Language'),
                TextColumn::make('imdb_votes')->label('IMDB Votes')->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(static fn (Status $state) => $state->present())
                    ->color(fn (Status $state) => match ($state) {
                        Status::WHITELISTED => 'success',
                        Status::BLACKLISTED, Status::BLACKLISTED_FINAL => 'danger',
                        Status::UNDECIDED => 'warning',
                        Status::UNREVIEWED => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Status::values())
                    ->default(Status::UNREVIEWED->value)
                    ->label(__('Status')),
                Filter::make('flagged_for_review')
                    ->label(__('Flagged for Review'))
                    ->query(fn (Builder $query): Builder => $query->where('flagged_for_review', '=', true)),
            ])
            ->actions([ViewAction::make()]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    InfolistGrid::make(1)->schema([
                        InfolistSection::make(__('Details'))->schema([
                            TextEntry::make('status')
                                ->label(__('Status'))
                                ->badge()
                                ->formatStateUsing(fn (Status $state) => $state->present())
                                ->color(fn (Status $state) => match ($state) {
                                    Status::WHITELISTED => 'success',
                                    Status::BLACKLISTED, Status::BLACKLISTED_FINAL => 'danger',
                                    Status::UNDECIDED => 'warning',
                                    Status::UNREVIEWED => 'gray',
                                }),
                            TextEntry::make('first_air_date')
                                ->label(__('First Aired'))
                                ->formatStateUsing(fn (?CarbonImmutable $state) => $state?->format('Y-m-d'))
                                ->color(
                                    fn (?CarbonImmutable $state) => $state?->gte(
                                        now()->subMonth()->startOfMonth(),
                                    ) ? 'warning' : null,
                                )
                                ->weight(
                                    fn (?CarbonImmutable $state) => $state?->gte(
                                        now()->subMonth()->startOfMonth(),
                                    ) ? FontWeight::Bold : null,
                                )
                                ->icon(
                                    fn (?CarbonImmutable $state) => $state?->gte(
                                        now()->subMonth()->startOfMonth(),
                                    ) ? 'heroicon-m-exclamation' : null,
                                ),
                            TextEntry::make('name')
                                ->label(__('Name'))
                                ->getStateUsing(
                                    fn (TvShow $record) => new HtmlString(
                                        $record->name.($record->original_name !== '' && $record->original_name !== '0' ? ' (<em>'.$record->original_name.'</em>)' : ''),
                                    ),
                                ),
                            TextEntry::make('type')->label(__('Type')),
                            TextEntry::make('languages')
                                ->label(__('Languages'))
                                ->badge()
                                ->formatStateUsing(fn (Language $state) => $state->name)
                                ->color(
                                    fn (Language $state, TvShow $record) => $state->language_code === $record->primary_language ? 'primary' : 'gray',
                                ),
                            TextEntry::make('countries.name')
                                ->label(__('Countries'))
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('genres.name')
                                ->label(__('Genres'))
                                ->badge()
                                ->color('warning'),
                            TextEntry::make('networks')
                                ->label(__('Networks'))
                                ->badge()
                                ->formatStateUsing(fn (Network $state) => $state->name)
                                ->color(
                                    fn (Network $state, TvShow $record) => $state->isWhitelisted() ? 'success' : 'gray',
                                ),
                            TextEntry::make('trakt_members')
                                ->label(__('Trakt'))
                                ->weight(fn (int $state) => $state < 100 || $state >= 5000 ? FontWeight::Bold : null)
                                ->color(fn (int $state) => match (true) {
                                    $state > 5000 => 'success',
                                    $state < 100 => 'danger',
                                    default => null,
                                }),
                            TextEntry::make('imdb_votes')
                                ->label(__('IMDB'))
                                ->formatStateUsing(
                                    fn (TvShow $record) => $record->imdb_id ? number_format(
                                        $record->imdb_score,
                                        2,
                                    ).' | '.$record->imdb_votes.'  votes' : '–',
                                )
                                ->url(
                                    fn (TvShow $record) => $record->imdb_id ? 'https://www.imdb.com/title/'.$record->imdb_id.'/' : null,
                                    shouldOpenInNewTab: true,
                                )
                                ->weight(fn (int $state) => $state < 100 || $state >= 5000 ? FontWeight::Bold : null)
                                ->color(fn (int $state) => match (true) {
                                    $state > 5000 => 'success',
                                    $state < 100 => 'danger',
                                    default => null,
                                }),
                        ])->grow(),
                    ])->grow(),
                    InfolistSection::make([
                        ImageEntry::make('poster')
                            ->hiddenLabel()
                            ->getStateUsing(
                                fn (TvShow $record) => $record->poster ? $record->present()->poster() : null,
                            )
                            ->checkFileExistence(false)
                            ->height('auto')
                            ->width(400),
                    ])->grow(false),
                ])->from('md'),
                InfolistSection::make(__('Streaming'))->schema([
                    TextEntry::make('us_watch_providers')
                        ->label(__('US'))
                        ->badge()
                        ->getStateUsing(
                            fn (TvShow $record) => $record->watchProviders->where('pivot.region', '=', 'US'),
                        )
                        ->formatStateUsing(fn (WatchProvider $state) => $state->name)
                        ->color(
                            fn (WatchProvider $state, TvShow $record) => $state->isWhitelisted() ? 'success' : 'gray',
                        ),
                    TextEntry::make('de_watch_providers')
                        ->label(__('DE'))
                        ->badge()
                        ->getStateUsing(
                            fn (TvShow $record) => $record->watchProviders->where('pivot.region', '=', 'DE'),
                        )
                        ->formatStateUsing(fn (WatchProvider $state) => $state->name)
                        ->color(
                            fn (WatchProvider $state, TvShow $record) => $state->isWhitelisted() ? 'success' : 'gray',
                        ),
                ]),
                InfolistSection::make(__('Overview'))->schema([
                    TextEntry::make('tmdb_id')
                        ->label(__('ID'))
                        ->url(
                            fn (TvShow $record) => 'https://www.themoviedb.org/tv/'.$record->tmdb_id,
                            shouldOpenInNewTab: true,
                        ),
                    TextEntry::make('runtime')
                        ->label(__('Runtime'))
                        ->formatStateUsing(fn (?int $state) => $state ? $state.' min.' : '–'),
                    TextEntry::make('content_rating')
                        ->label(__('Rating')),
                    TextEntry::make('production_status')
                        ->label(__('Status'))
                        ->formatStateUsing(fn (?string $state) => $state ? Str::headline($state) : '–'),
                    TextEntry::make('summary')
                        ->label(__('Summary')),
                    TextEntry::make('overview')
                        ->label(__('Overview')),
                ]),
            ])->columns(1);
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

    public static function getNavigationGroup(): string
    {
        return __('Television');
    }
}
