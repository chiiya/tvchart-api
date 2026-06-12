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
        $pending = TvShow::factory()->pendingReview()
            ->withSeason(now()->addWeeks(2))
            ->create();
        $flagged = TvShow::factory()->pendingReview()
            ->withSeason(now()->subMonth())
            ->create([
                'status' => Status::BLACKLISTED,
                'flagged_for_review' => true,
            ]);

        TvShow::factory()->pendingReview()->withSeason(now())->create(['status' => Status::WHITELISTED]);
        TvShow::factory()->pendingReview()->withSeason(now())->create(['poster' => null]);
        TvShow::factory()->pendingReview()->withSeason(now())->create(['overview' => null]);
        TvShow::factory()->pendingReview()->create(); // no qualifying season
        TvShow::factory()->pendingReview()->withSeason(now()->setDate(2021, 6, 1))->create(); // before archive start
        TvShow::factory()->pendingReview()->withSeason(now()->addMonths(6))->create(); // beyond review horizon

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertCount(2, $ids);
        $this->assertContains($pending->tmdb_id, $ids);
        $this->assertContains($flagged->tmdb_id, $ids);
    }

    public function test_orders_flagged_shows_first_then_by_air_date_proximity(): void
    {
        $flaggedOld = TvShow::factory()->pendingReview()
            ->withSeason(now()->subYears(2))
            ->create([
                'status' => Status::BLACKLISTED,
                'flagged_for_review' => true,
                'trakt_members' => 10,
            ]);
        $airingNow = TvShow::factory()->pendingReview()->popular()
            ->withSeason(now())
            ->create();
        $upcoming = TvShow::factory()->pendingReview()
            ->withSeason(now()->addWeeks(3))
            ->create();
        $airedAwhileAgo = TvShow::factory()->pendingReview()
            ->withSeason(now()->subMonths(8))
            ->create();

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertSame(
            [$flaggedOld->tmdb_id, $airingNow->tmdb_id, $upcoming->tmdb_id, $airedAwhileAgo->tmdb_id],
            $ids,
        );
    }

    public function test_prioritises_recency_over_popularity(): void
    {
        $recentUnpopular = TvShow::factory()->pendingReview()
            ->withSeason(now()->subWeek())
            ->create(['trakt_members' => 10, 'imdb_votes' => 10]);
        $oldPopular = TvShow::factory()->pendingReview()->popular()
            ->withSeason(now()->subYears(3))
            ->create();

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertSame([$recentUnpopular->tmdb_id, $oldPopular->tmdb_id], $ids);
    }

    public function test_uses_latest_season_air_date_not_premiere(): void
    {
        $longRunning = TvShow::factory()->pendingReview()
            ->withSeason(now()->subWeeks(2))
            ->create(['first_air_date' => now()->subYears(11)]);

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertContains($longRunning->tmdb_id, $ids);
    }

    public function test_ignores_far_future_seasons_when_an_in_horizon_season_exists(): void
    {
        $returning = TvShow::factory()->pendingReview()
            ->withSeason(now()->subMonth(), number: 4)
            ->withSeason(now()->addMonths(6), number: 5)
            ->create();

        $ids = TvShow::query()->pendingReview()->pluck('tmdb_id')->all();

        $this->assertContains($returning->tmdb_id, $ids);
    }
}
