<?php declare(strict_types=1);

namespace App\V1\Tests\Feature;

use App\Domain\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_curated_genres_sorted_by_name(): void
    {
        Genre::factory()->create(['name' => 'Drama']);
        Genre::factory()->create(['name' => 'Comedy']);

        $response = $this->getJson('/api/v1/facets');

        $response->assertOk();
        $this->assertSame(['Comedy', 'Drama'], $response->json('genres'));
    }

    public function test_excludes_blacklisted_genres(): void
    {
        Genre::factory()->create(['name' => 'Drama']);
        Genre::factory()->create(['name' => 'Reality']);

        config()->set('tv-chart.blacklist.genres', ['Reality']);

        $response = $this->getJson('/api/v1/facets');

        $response->assertOk();
        $this->assertSame(['Drama'], $response->json('genres'));
    }

    public function test_returns_curated_networks_in_configured_order(): void
    {
        config()->set('tv-chart.facets.networks', ['Netflix', 'Prime Video', 'HBO']);

        $response = $this->getJson('/api/v1/facets');

        $response->assertOk();
        $this->assertSame(['Netflix', 'Prime Video', 'HBO'], $response->json('networks'));
    }
}
