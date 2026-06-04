<?php declare(strict_types=1);

namespace App\Domain\Console;

use App\Domain\Mail\WeeklyReviewDigest;
use App\Domain\Models\TvShow;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReviewDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'tvchart:digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the weekly email digest about shows pending manual review.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipients = config('tv-chart.digest.recipients');

        if ($recipients === []) {
            $recipients = FilamentUser::query()->pluck('email')->all();
        }

        if ($recipients === []) {
            $this->warn('No digest recipients configured, skipping.');

            return self::SUCCESS;
        }

        $pendingCount = TvShow::query()->pendingReview()->count();
        $shows = TvShow::query()->pendingReview()->limit((int) config('tv-chart.digest.limit'))->get();
        $importedLastWeek = TvShow::query()->where('created_at', '>', now()->subWeek())->count();

        Mail::to($recipients)->queue(new WeeklyReviewDigest($pendingCount, $importedLastWeek, $shows));

        $this->comment('Review digest has been queued.');

        return self::SUCCESS;
    }
}
