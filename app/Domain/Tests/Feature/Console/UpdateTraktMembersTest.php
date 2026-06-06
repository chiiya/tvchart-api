<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Console;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class UpdateTraktMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_member_counts_and_sync_timestamps(): void
    {
        Http::fake([
            'api.trakt.tv/shows/tt0000001/stats' => Http::response([
                'collectors' => 1234,
            ]),
        ]);
        $show = TvShow::factory()->create([
            'imdb_id' => 'tt0000001',
        ]);
        $finallyBlacklisted = TvShow::factory()->create([
            'imdb_id' => 'tt0000002',
            'status' => Status::BLACKLISTED_FINAL,
        ]);
        $withoutImdbId = TvShow::factory()->create([
            'imdb_id' => null,
        ]);

        $exitCode = Artisan::call('tvchart:update:trakt');

        $this->assertSame(0, $exitCode);
        $this->assertSame(1234, $show->refresh()->trakt_members);
        $this->assertNotNull($show->trakt_synced_at);
        $this->assertNull($finallyBlacklisted->refresh()->trakt_synced_at);
        $this->assertNull($withoutImdbId->refresh()->trakt_synced_at);
        Http::assertSentCount(1);
    }

    public function test_processes_never_synced_shows_first(): void
    {
        Http::fake([
            'api.trakt.tv/*' => Http::response([
                'collectors' => 50,
            ]),
        ]);
        $syncedRecently = TvShow::factory()->create([
            'imdb_id' => 'tt0000001',
            'trakt_synced_at' => now()->subDay(),
        ]);
        $neverSynced = TvShow::factory()->create([
            'imdb_id' => 'tt0000002',
        ]);

        Artisan::call('tvchart:update:trakt', ['--limit' => 1]);

        $this->assertSame(50, $neverSynced->refresh()->trakt_members);
        $this->assertSame(0, $syncedRecently->refresh()->trakt_members);
    }

    public function test_marks_shows_unknown_to_trakt_as_synced(): void
    {
        Http::fake([
            'api.trakt.tv/*' => Http::response(null, 404),
        ]);
        $show = TvShow::factory()->create([
            'imdb_id' => 'tt0000001',
            'trakt_members' => 42,
        ]);

        $exitCode = Artisan::call('tvchart:update:trakt');

        $this->assertSame(0, $exitCode);
        $this->assertNotNull($show->refresh()->trakt_synced_at);
        $this->assertSame(42, $show->trakt_members);
    }

    public function test_aborts_on_unexpected_trakt_error(): void
    {
        Http::fake([
            'api.trakt.tv/*' => Http::response(null, 500),
        ]);
        $show = TvShow::factory()->create([
            'imdb_id' => 'tt0000001',
        ]);

        $exitCode = Artisan::call('tvchart:update:trakt');

        $this->assertSame(1, $exitCode);
        $this->assertNull($show->refresh()->trakt_synced_at);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Sleep::fake();
    }
}
