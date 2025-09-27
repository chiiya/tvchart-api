<?php declare(strict_types=1);

namespace App\Filament\Resources\TvShowResource\Pages;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use App\Filament\Resources\TvShowResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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

    public function whitelist(): Redirector|RedirectResponse|null
    {
        $this->record->update([
            'status' => Status::WHITELISTED,
            'flagged_for_review' => false,
            'status_updated_at' => now(),
        ]);
        Notification::make()->title('Record whitelisted successfully.')->success()->send();

        return $this->nextUnreviewedRecord();
    }

    public function blacklist(BlacklistReason $reason): Redirector|RedirectResponse|null
    {
        $this->record->update([
            'status' => Status::BLACKLISTED,
            'blacklist_reason' => $reason,
            'status_updated_at' => now(),
        ]);
        Notification::make()->title('Record blacklisted successfully.')->success()->send();

        return $this->nextUnreviewedRecord();
    }

    public function blacklistFinal(): void
    {
        $this->record->update([
            'status' => Status::BLACKLISTED_FINAL,
            'flagged_for_review' => false,
            'status_updated_at' => now(),
        ]);
        Notification::make()->title('Record blacklisted successfully.')->success()->send();
    }

    public function undecided(): void
    {
        $this->record->update([
            'status' => Status::UNDECIDED,
            'flagged_for_review' => false,
            'status_updated_at' => now(),
        ]);
        Notification::make()->title('Record updated successfully.')->success()->send();
    }

    public function nextUnreviewedRecord(): Redirector|RedirectResponse|null
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
            ->where('first_air_date', '<', now()->addMonths(2))
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
                ->icon('heroicon-m-question-mark-circle')
                ->label(__('Undecided')),
            Action::make('whitelist')
                ->action('whitelist')
                ->color('success')
                ->icon('heroicon-m-check-circle')
                ->label(__('Whitelist')),
            ActionGroup::make([
                Action::make('blacklistUnavailable')
                    ->label(BlacklistReason::UNAVAILABLE->present())
                    ->action(fn () => $this->blacklist(BlacklistReason::UNAVAILABLE)),
                Action::make('blacklistInappropriate')
                    ->label(BlacklistReason::INAPPROPRIATE->present())
                    ->action(fn () => $this->blacklist(BlacklistReason::INAPPROPRIATE)),
                Action::make('blacklistGenre')
                    ->label(BlacklistReason::GENRE->present())
                    ->action(fn () => $this->blacklist(BlacklistReason::GENRE)),
                Action::make('blacklistNetwork')
                    ->label(BlacklistReason::NETWORK->present())
                    ->action(fn () => $this->blacklist(BlacklistReason::NETWORK)),
            ])
                ->icon('heroicon-m-x-circle')
                ->label(__('Blacklist'))
                ->color('danger')
                ->button()
                ->visible(
                    fn () => ! $this->record->flagged_for_review && $this->record->status !== Status::BLACKLISTED_FINAL,
                ),
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
