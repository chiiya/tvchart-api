<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Console;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BlacklistStaleShowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_blacklists_stale_unreviewed_shows(): void
    {
        Queue::fake();
        $stale = TvShow::factory()->stale()->create();
        $recent = TvShow::factory()->create(['first_air_date' => now()->subMonths(3)]);
        $popular = TvShow::factory()->stale()->popular()->create();
        $whitelisted = TvShow::factory()->stale()->create(['status' => Status::WHITELISTED]);

        $exitCode = Artisan::call('tvchart:blacklist-stale');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('1 stale shows have been blacklisted.', Artisan::output());

        $this->assertSame(Status::BLACKLISTED, $stale->refresh()->status);
        $this->assertSame(BlacklistReason::NO_TRACTION, $stale->blacklist_reason);
        $this->assertNotNull($stale->status_updated_at);
        $this->assertSame(Status::UNREVIEWED, $recent->refresh()->status);
        $this->assertSame(Status::UNREVIEWED, $popular->refresh()->status);
        $this->assertSame(Status::WHITELISTED, $whitelisted->refresh()->status);
        Queue::assertNothingPushed();
    }
}
