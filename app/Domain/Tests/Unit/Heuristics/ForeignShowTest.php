<?php declare(strict_types=1);

namespace App\Domain\Tests\Unit\Heuristics;

use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Heuristics\ForeignShow;
use App\Domain\Models\Country;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use App\Domain\Models\WatchProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForeignShowTest extends TestCase
{
    use RefreshDatabase;
    private ForeignShow $heuristic;

    public function test_ignores_english_show(): void
    {
        $show = TvShow::factory()->create([
            'primary_language' => 'en',
        ]);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_foreign_show_from_whitelisted_country(): void
    {
        Country::factory()->create([
            'country_code' => 'US',
        ]);
        $show = TvShow::factory()->foreign()->create();
        $show->countries()->attach('US');

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_blacklists_recently_aired_foreign_show_without_international_availability(): void
    {
        $show = TvShow::factory()->foreign()->create([
            'first_air_date' => now()->subMonths(2),
        ]);

        $status = $this->heuristic->apply($show);

        $this->assertSame(Status::BLACKLISTED, $status);
        $this->assertSame(BlacklistReason::UNAVAILABLE, $this->heuristic->reason());
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $show->tmdb_id,
            'description' => 'Blacklisted due to missing international availability.',
        ]);
    }

    public function test_ignores_foreign_show_on_international_network(): void
    {
        $network = Network::factory()->create([
            'name' => 'Netflix',
        ]);
        $show = TvShow::factory()->foreign()->create();
        $show->networks()->attach($network);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_ignores_foreign_show_streaming_in_whitelisted_region(): void
    {
        Country::factory()->create([
            'country_code' => 'US',
        ]);
        $provider = WatchProvider::factory()->create();
        $show = TvShow::factory()->foreign()->create();
        $show->watchProviders()->attach($provider, ['region' => 'US']);

        $this->assertNull($this->heuristic->apply($show));
    }

    public function test_blacklists_show_with_blacklisted_language_on_niche_provider(): void
    {
        Country::factory()->create([
            'country_code' => 'US',
        ]);
        $provider = WatchProvider::factory()->create();
        $show = TvShow::factory()->create([
            'primary_language' => 'ja',
        ]);
        $show->watchProviders()->attach($provider, ['region' => 'US']);

        $this->assertSame(Status::BLACKLISTED, $this->heuristic->apply($show));
    }

    public function test_ignores_show_with_blacklisted_language_on_whitelisted_provider(): void
    {
        Country::factory()->create([
            'country_code' => 'US',
        ]);
        $provider = WatchProvider::factory()->create([
            'name' => 'Netflix',
        ]);
        $show = TvShow::factory()->create([
            'primary_language' => 'ja',
        ]);
        $show->watchProviders()->attach($provider, ['region' => 'US']);

        $this->assertNull($this->heuristic->apply($show));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->heuristic = new ForeignShow;
    }
}
