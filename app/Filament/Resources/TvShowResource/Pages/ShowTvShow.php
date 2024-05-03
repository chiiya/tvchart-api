<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use App\Filament\Resources\TvShowResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * @property TvShow $record
 */
class ShowTvShow extends ViewRecord
{
    protected static string $resource = TvShowResource::class;

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

    public function nextUnreviewedRecord(): null|Redirector|RedirectResponse
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
            ->orderByDesc('first_air_date')
            ->select(['tmdb_id'])
            ->first();

        if ($record === null) {
            Notification::make()->title('All records reviewed.')->send();

            return null;
        }

        return redirect()->route('filament.admin.resources.tv-shows.view', $record);
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
}
