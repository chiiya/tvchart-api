<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Pipelines;

use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Models\Country;
use App\Domain\Models\Language;
use App\Domain\Models\TvShow;
use App\Domain\Models\WatchProvider;
use App\Domain\Pipelines\UpdateTvShowPipeline;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdateTvShowPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_does_not_call_trakt_when_disabled(): void
    {
        $this->fakeSources();

        UpdateTvShowPipeline::run(new UpdateTvShowData(id: 1399, show: new TvShow));

        Http::assertNotSent(fn ($request) => str_contains($request->url(), 'api.trakt.tv'));
        $this->assertSame('Game of Thrones', TvShow::query()->where('tmdb_id', '=', 1399)->firstOrFail()->name);
    }

    public function test_persists_tmdb_data_when_trakt_is_unavailable(): void
    {
        config()->set('tv-chart.trakt.enabled', true);
        $this->fakeSources(traktStatus: 500);

        UpdateTvShowPipeline::run(new UpdateTvShowData(id: 1399, show: new TvShow));

        $show = TvShow::query()->where('tmdb_id', '=', 1399)->firstOrFail();
        $this->assertSame('Game of Thrones', $show->name);
        $this->assertDatabaseHas('tv_show_watch_provider', [
            'tv_show_id' => 1399,
            'watch_provider_id' => 384,
            'region' => 'US',
        ]);
    }

    public function test_persists_tmdb_data_when_omdb_is_unavailable(): void
    {
        $this->fakeSources(omdbStatus: 500);

        UpdateTvShowPipeline::run(new UpdateTvShowData(id: 1399, show: new TvShow));

        $show = TvShow::query()->where('tmdb_id', '=', 1399)->firstOrFail();
        $this->assertSame('Game of Thrones', $show->name);
        $this->assertDatabaseHas('tv_show_watch_provider', [
            'tv_show_id' => 1399,
            'watch_provider_id' => 384,
            'region' => 'US',
        ]);
    }

    public function test_refreshes_watch_providers_on_subsequent_runs(): void
    {
        $this->fakeSources(traktStatus: 500);
        $stale = WatchProvider::factory()->create(['tmdb_id' => 8]);
        $show = TvShow::factory()->create(['tmdb_id' => 1399]);
        $show->watchProviders()->attach([
            ['watch_provider_id' => $stale->tmdb_id, 'region' => 'US'],
        ]);

        UpdateTvShowPipeline::run(new UpdateTvShowData(id: 1399, show: new TvShow));

        $this->assertDatabaseMissing('tv_show_watch_provider', [
            'tv_show_id' => 1399,
            'watch_provider_id' => 8,
        ]);
        $this->assertDatabaseHas('tv_show_watch_provider', [
            'tv_show_id' => 1399,
            'watch_provider_id' => 384,
        ]);
    }

    private function fakeSources(int $traktStatus = 200, int $omdbStatus = 200): void
    {
        Country::factory()->create(['country_code' => 'US']);
        Language::query()->create(['language_code' => 'en', 'name' => 'English']);
        WatchProvider::factory()->create(['tmdb_id' => 384]);

        Http::fake([
            'api.themoviedb.org/3/tv/1399*' => Http::response([
                'id' => 1399,
                'status' => 'Ended',
                'vote_count' => 12000,
                'name' => 'Game of Thrones',
                'original_name' => 'Game of Thrones',
                'origin_country' => ['US'],
                'adult' => false,
                'number_of_seasons' => 0,
                'type' => 'Scripted',
                'in_production' => false,
                'first_air_date' => '2011-04-17',
                'overview' => 'Seven noble families fight for control.',
                'original_language' => 'en',
                'episode_run_time' => [60],
                'languages' => ['en'],
                'seasons' => [],
                'networks' => [],
                'production_companies' => [],
                'production_countries' => [],
                'genres' => [['id' => 18, 'name' => 'Drama']],
                'external_ids' => ['imdb_id' => 'tt0944947'],
                'content_ratings' => ['results' => [['iso_3166_1' => 'US', 'rating' => 'TV-MA']]],
                'watch/providers' => [
                    'results' => [
                        'US' => [
                            'link' => 'https://www.themoviedb.org/tv/1399/watch',
                            'flatrate' => [[
                                'display_priority' => 1,
                                'logo_path' => '/logo.jpg',
                                'provider_name' => 'Max',
                                'provider_id' => 384,
                            ]],
                        ],
                    ],
                ],
            ]),
            'www.omdbapi.com*' => $omdbStatus === 200
                ? Http::response(['Plot' => 'Seven noble families.', 'Genre' => 'Drama', 'Response' => 'True'])
                : Http::response(null, $omdbStatus),
            'api.trakt.tv/*' => $traktStatus === 200
                ? Http::response(['collectors' => 500_000, 'runtime' => 60, 'genres' => ['drama']])
                : Http::response(null, $traktStatus),
        ]);
    }
}
