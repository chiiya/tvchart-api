<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use App\Domain\Services\CachingService;
use App\Filament\Resources\TvShowResource;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Concerns\HasRecordBreadcrumb;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ShowTvShow extends Page
{
    use HasRecordBreadcrumb;
    use HasRelationManagers;
    use InteractsWithRecord;
    public static string $view = 'filament.pages.tv-show';
    public static ?string $slug = 'show';
    protected static string $resource = TvShowResource::class;

    /** @var TvShow */
    public $record;
    public Collection $languages;
    public Collection $usWatchProviders;
    public Collection $deWatchProviders;

    public function mount(string|int $record): void
    {
        static::authorizeResourceAccess();
        $this->record = TvShow::query()
            ->where('tmdb_id', '=', (int) $record)
            ->with(['seasons', 'genres', 'countries', 'languages', 'networks', 'watchProviders'])
            ->firstOrFail();
        $this->usWatchProviders = $this->record->watchProviders->where('pivot.region', '=', 'US');
        $this->deWatchProviders = $this->record->watchProviders->where('pivot.region', '=', 'DE');
        $this->languages = resolve(CachingService::class)->getLanguages();
    }

    public function whitelist(): void
    {
        $this->record->update([
            'status' => Status::WHITELISTED,
            'flagged_for_review' => false,
        ]);
        Notification::make()->title('Record whitelisted successfully.')->success()->send();
    }

    public function blacklist(): void
    {
        $this->record->update(['status' => Status::BLACKLISTED]);
        Notification::make()->title('Record blacklisted successfully.')->success()->send();
    }

    public function blacklistFinal(): void
    {
        $this->record->update([
            'status' => Status::BLACKLISTED_FINAL,
            'flagged_for_review' => false,
        ]);
        Notification::make()->title('Record blacklisted successfully.')->success()->send();
    }

    public function undecided(): void
    {
        $this->record->update([
            'status' => Status::UNDECIDED,
            'flagged_for_review' => false,
        ]);
        Notification::make()->title('Record updated successfully.')->success()->send();
    }

    public function nextUnreviewedRecord()
    {
        $record = TvShow::query()
            ->where(
                fn (Builder $builder) => $builder
                    ->where('status', '=', Status::UNREVIEWED)
                    ->orWhere('flagged_for_review', '=', true),
            )
            ->whereNotNull('poster')
            ->whereNotNull('overview')
            ->whereNotNull('first_air_date')
            ->orderByDesc('imdb_votes')
            ->select(['tmdb_id'])
            ->first();

        if ($record === null) {
            Notification::make()->title('All records reviewed.')->send();

            return;
        }

        return redirect()->route('filament.resources.tv-shows.view', $record);
    }

    protected function getActions(): array
    {
        return [
            Action::make('undecided')
                ->action('undecided')
                ->color('warning')
                ->label(__('Undecided')),
            Action::make('whitelist')
                ->action('whitelist')
                ->color('success')
                ->label(__('Whitelist')),
            Action::make('blacklist')
                ->action('blacklist')
                ->color('danger')
                ->visible(
                    fn () => ! $this->record->flagged_for_review && $this->record->status !== Status::BLACKLISTED_FINAL,
                )
                ->label(__('Blacklist')),
            Action::make('blacklistFinal')
                ->action('blacklistFinal')
                ->color('danger')
                ->visible(fn () => $this->record->flagged_for_review)
                ->label(__('Blacklist (Final)')),
            Action::make('next')
                ->action('nextUnreviewedRecord')
                ->label(__('Review Next')),
        ];
    }

    protected function getTitle(): string
    {
        return __('TV Show Details');
    }
}
