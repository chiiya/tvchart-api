<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Models;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendingReviewScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_includes_reviewable_shows(): void
    {
        $pending = TvShow::factory()->pendingReview()->create();
        $flagged = TvShow::factory()->pendingReview()->create([
            'status' => Status::BLACKLISTED,
            'flagged_for_review' => true,
        ]);
        TvShow::factory()->pendingReview()->create(['status' => Status::WHITELISTED]);
        TvShow::factory()->pendingReview()->create(['poster' => null]);
        TvShow::factory()->pendingReview()->create(['overview' => null]);
        TvShow::factory()->pendingReview()->create(['first_air_date' => null]);
        TvShow::factory()->pendingReview()->create(['first_air_date' => now()->addMonths(3)]);

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertCount(2, $ids);
        $this->assertContains($pending->tmdb_id, $ids);
        $this->assertContains($flagged->tmdb_id, $ids);
    }

    public function test_orders_upcoming_and_popular_shows_first(): void
    {
        $airedUnpopular = TvShow::factory()->pendingReview()->create([
            'first_air_date' => now()->subMonths(2),
            'trakt_members' => 10,
        ]);
        $airedPopular = TvShow::factory()->pendingReview()->popular()->create([
            'first_air_date' => now()->subMonths(2),
        ]);
        $upcoming = TvShow::factory()->pendingReview()->create([
            'first_air_date' => now()->addWeeks(2),
        ]);

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertSame([$upcoming->tmdb_id, $airedPopular->tmdb_id, $airedUnpopular->tmdb_id], $ids);
    }
}
