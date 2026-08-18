<?php declare(strict_types=1);

namespace App\Domain\Tests\Unit\Heuristics;

use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\PopularShow;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopularShowTest extends TestCase
{
    use RefreshDatabase;
    private PopularShow $heuristic;

    public function test_whitelists_popular_show_without_trakt_data(): void
    {
        $show = $this->createShow(imdbVotes: 50_000, traktMembers: 0);

        $status = $this->heuristic->apply($show);

        $this->assertSame(Status::WHITELISTED, $status);
        $this->assertNull($this->heuristic->reason());
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $show->tmdb_id,
            'description' => 'Whitelisted due to popularity.',
        ]);
    }

    public function test_ignores_show_without_imdb_traction(): void
    {
        $show = $this->createShow(imdbVotes: 9_999, traktMembers: 50_000);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_foreign_language_show(): void
    {
        $show = $this->createShow(imdbVotes: 50_000, traktMembers: 0, language: 'de');

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_show_without_whitelisted_network(): void
    {
        $show = $this->createShow(imdbVotes: 50_000, traktMembers: 0, network: 'Some Local Channel');

        $this->assertNull($this->heuristic->apply($show));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->heuristic = new PopularShow;
    }

    private function createShow(
        int $imdbVotes,
        int $traktMembers,
        string $language = 'en',
        string $network = 'Netflix',
    ): TvShow {
        $show = TvShow::factory()->create([
            'imdb_votes' => $imdbVotes,
            'trakt_members' => $traktMembers,
            'primary_language' => $language,
        ]);
        $show->networks()->attach(Network::factory()->create(['name' => $network]));

        return $show->refresh();
    }
}
