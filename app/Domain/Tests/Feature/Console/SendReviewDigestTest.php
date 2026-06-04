<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Console;

use App\Domain\Mail\WeeklyReviewDigest;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendReviewDigestTest extends TestCase
{
    use RefreshDatabase;

    public function test_queues_digest_with_pending_shows(): void
    {
        Mail::fake();
        config([
            'tv-chart.digest.recipients' => ['admin@example.com'],
        ]);
        $upcoming = TvShow::factory()->pendingReview()->create();
        $aired = TvShow::factory()->pendingReview()->popular()->create([
            'first_air_date' => now()->subMonth(),
        ]);

        $exitCode = Artisan::call('tvchart:digest');

        $this->assertSame(0, $exitCode);
        Mail::assertQueued(WeeklyReviewDigest::class, fn (WeeklyReviewDigest $mail) => $mail->hasTo('admin@example.com')
                && $mail->pendingCount === 2
                && $mail->importedLastWeek === 2
                && $mail->shows->pluck('tmdb_id')->all() === [$upcoming->tmdb_id, $aired->tmdb_id]);
    }

    public function test_renders_digest_view(): void
    {
        config([
            'tv-chart.digest.recipients' => ['admin@example.com'],
        ]);
        $shows = TvShow::factory()->pendingReview()->count(2)->create([
            'name' => 'Some Pending Show',
        ]);

        $mail = new WeeklyReviewDigest(2, 5, $shows);

        $html = $mail->render();
        $this->assertStringContainsString('2', $html);
        $this->assertStringContainsString('Some Pending Show', $html);
    }

    public function test_skips_digest_without_recipients(): void
    {
        Mail::fake();
        config([
            'tv-chart.digest.recipients' => [],
        ]);

        $exitCode = Artisan::call('tvchart:digest');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('No digest recipients configured, skipping.', Artisan::output());
        Mail::assertNothingQueued();
    }
}
