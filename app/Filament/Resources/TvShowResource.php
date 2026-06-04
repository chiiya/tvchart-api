<?php declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\Country;
use App\Domain\Models\Language;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use App\Domain\Models\WatchProvider;
use App\Filament\Resources\TvShowResource\Pages\ListTvShows;
use App\Filament\Resources\TvShowResource\Pages\ShowTvShow;
use App\Filament\Resources\TvShowResource\RelationManagers\TvSeasonsRelationManager;
use BackedEnum;
use Carbon\CarbonImmutable;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
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
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-film';
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
                Filter::make('first_air_date')
                    ->schema([
                        Fieldset::make()
                            ->columns(1)
                            ->schema([
                                Select::make('month')
                                    ->label(__('Month'))
                                    ->options([
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ])
                                    ->required(),
                                Select::make('year')
                                    ->label(__('Year'))
                                    ->options(
                                        collect(range(now()->year, 2020))
                                            ->mapWithKeys(fn (int $year): array => [$year => (string) $year])
                                            ->all(),
                                    )
                                    ->required(),
                            ]),
                    ])
                    ->query(
                        fn (Builder $query, array $data): Builder => $query
                            ->when(
                                $data['month'] && $data['year'],
                                fn (Builder $query) => $query
                                    ->whereHas(
                                        'seasons',
                                        fn (Builder $query) => $query
                                            ->where(
                                                'first_air_date',
                                                '>=',
                                                CarbonImmutable::createFromDate(
                                                    $data['year'],
                                                    $data['month'],
                                                )->startOfMonth(),
                                            )
                                            ->where(
                                                'first_air_date',
                                                '<=',
                                                CarbonImmutable::createFromDate(
                                                    $data['year'],
                                                    $data['month'],
                                                )->endOfMonth(),
                                            ),
                                    ),
                            ),
                    ),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'md' => 3,
                ])->schema([
                    Section::make()
                        ->heading(__('Details'))
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('Name'))
                                ->size(TextSize::Large)
                                ->weight(FontWeight::Bold)
                                ->color('primary')
                                ->getStateUsing(
                                    fn (TvShow $record) => new HtmlString(
                                        $record->name.($record->original_name !== '' && $record->original_name !== '0' && $record->original_name !== $record->name ? ' (<em>'.$record->original_name.'</em>)' : ''),
                                    ),
                                ),
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
                                        now()->subMonths(2)->startOfMonth(),
                                    ) ? 'warning' : null,
                                )
                                ->weight(
                                    fn (?CarbonImmutable $state) => $state?->gte(
                                        now()->subMonths(2)->startOfMonth(),
                                    ) ? FontWeight::Bold : null,
                                )
                                ->icon(
                                    fn (?CarbonImmutable $state) => $state?->gte(
                                        now()->subMonths(2)->startOfMonth(),
                                    ) ? 'heroicon-m-exclamation-triangle' : null,
                                )
                                ->iconColor('warning'),
                            TextEntry::make('blacklist_reason')
                                ->label(__('Blacklist Reason'))
                                ->formatStateUsing(fn (BlacklistReason $state) => $state->present()),
                            TextEntry::make('type')->label(__('Type')),
                            TextEntry::make('languages')
                                ->label(__('Languages'))
                                ->badge()
                                ->formatStateUsing(fn (Language $state) => $state->name)
                                ->color(
                                    fn (Language $state, TvShow $record) => $state->language_code === $record->primary_language ? 'primary' : 'gray',
                                ),
                            TextEntry::make('countries')
                                ->label(__('Countries'))
                                ->badge()
                                ->formatStateUsing(fn (Country $state) => $state->name)
                                ->color(
                                    fn (Country $state, TvShow $record) => $state->isWhitelisted() ? 'success' : 'gray',
                                ),
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
                        ])
                        ->columnSpan(2),
                    Section::make([
                        ImageEntry::make('poster')
                            ->hiddenLabel()
                            ->getStateUsing(
                                fn (TvShow $record) => $record->poster ? $record->present()->poster() : null,
                            )
                            ->checkFileExistence(false)
                            ->view('filament.image'),
                        TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->date(),
                        TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->date(),
                    ])->columnSpan(1),
                ]),
                Section::make(__('Streaming'))->schema([
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
                Section::make(__('Overview'))->schema([
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
