<?php declare(strict_types=1);

namespace App\Domain\Mail;

use App\Domain\Models\TvShow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyReviewDigest extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @param Collection<int, TvShow> $shows
     */
    public function __construct(
        public readonly int $pendingCount,
        public readonly int $importedLastWeek,
        public readonly Collection $shows,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "TV Chart: {$this->pendingCount} shows pending review");
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.weekly-review-digest');
    }
}
