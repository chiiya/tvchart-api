<?php declare(strict_types=1);

namespace App\Domain\Tests\Unit\Heuristics;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\StaleShow;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaleShowTest extends TestCase
{
    use RefreshDatabase;
    private StaleShow $heuristic;

    public function test_blacklists_show_that_aired_long_ago_without_traction(): void
    {
        $show = TvShow::factory()->stale()->create();

        $status = $this->heuristic->apply($show);

        $this->assertSame(Status::BLACKLISTED, $status);
        $this->assertSame(BlacklistReason::NO_TRACTION, $this->heuristic->reason());
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $show->tmdb_id,
            'description' => 'Blacklisted due to lack of traction.',
        ]);
    }

    public function test_ignores_recently_aired_show(): void
    {
        $show = TvShow::factory()->create([
            'first_air_date' => now()->subMonths(6),
            'imdb_votes' => 0,
            'trakt_members' => 0,
        ]);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_show_with_imdb_traction(): void
    {
        $show = TvShow::factory()->create([
            'first_air_date' => now()->subMonths(18),
            'imdb_votes' => 1000,
            'trakt_members' => 0,
        ]);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_show_with_trakt_traction(): void
    {
        $show = TvShow::factory()->create([
            'first_air_date' => now()->subMonths(18),
            'imdb_votes' => 0,
            'trakt_members' => 1000,
        ]);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_show_without_air_date(): void
    {
        $show = TvShow::factory()->create([
            'first_air_date' => null,
            'imdb_votes' => 0,
            'trakt_members' => 0,
        ]);

        $this->assertNull($this->heuristic->apply($show));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->heuristic = new StaleShow;
    }
}
