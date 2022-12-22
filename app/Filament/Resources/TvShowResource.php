<?php declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Models\TvShow;
use App\Filament\Resources\TvShowResource\Pages\CreateTvShow;
use App\Filament\Resources\TvShowResource\Pages\EditTvShow;
use App\Filament\Resources\TvShowResource\Pages\ListTvShows;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;

class TvShowResource extends Resource
{
    protected static ?string $model = TvShow::class;
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tmdb_id')->label('ID')->searchable(),
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('release_year')->label('Release Year'),
                TextColumn::make('type')->label('Type'),
                TextColumn::make('primary_language')->label('Language'),
            ])
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTvShows::route('/'),
            'create' => CreateTvShow::route('/create'),
            'edit' => EditTvShow::route('/{record}/edit'),
        ];
    }
}
