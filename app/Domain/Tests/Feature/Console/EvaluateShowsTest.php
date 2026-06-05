<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Console;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EvaluateShowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_blacklists_stale_unreviewed_shows(): void
    {
        Queue::fake();
        $stale = TvShow::factory()->stale()->create();
        $recent = TvShow::factory()->create([
            'first_air_date' => now()->subMonths(3),
        ]);
        $popular = TvShow::factory()->stale()->popular()->create();
        $whitelisted = TvShow::factory()->stale()->create([
            'status' => Status::WHITELISTED,
        ]);

        $exitCode = Artisan::call('tvchart:evaluate');

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('1 shows have been updated.', Artisan::output());
        $this->assertSame(Status::BLACKLISTED, $stale->refresh()->status);
        $this->assertSame(BlacklistReason::NO_TRACTION, $stale->blacklist_reason);
        $this->assertNotNull($stale->status_updated_at);
        $this->assertSame(Status::UNREVIEWED, $recent->refresh()->status);
        $this->assertSame(Status::UNREVIEWED, $popular->refresh()->status);
        $this->assertSame(Status::WHITELISTED, $whitelisted->refresh()->status);
        Queue::assertNothingPushed();
    }

    public function test_blacklists_unavailable_foreign_shows(): void
    {
        $foreign = TvShow::factory()->foreign()->create([
            'first_air_date' => now()->subMonths(2),
        ]);
        $netflixOriginal = TvShow::factory()->foreign()->create([
            'first_air_date' => now()->subMonths(2),
        ]);
        $netflixOriginal->networks()->attach(Network::factory()->create([
            'name' => 'Netflix',
        ]));

        Artisan::call('tvchart:evaluate');

        $this->assertSame(Status::BLACKLISTED, $foreign->refresh()->status);
        $this->assertSame(BlacklistReason::UNAVAILABLE, $foreign->blacklist_reason);
        $this->assertSame(Status::UNREVIEWED, $netflixOriginal->refresh()->status);
    }
}
