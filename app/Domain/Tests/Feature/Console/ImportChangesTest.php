<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Console;

use App\Domain\Jobs\UpdateTvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ImportChangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_splits_a_long_range_into_windows_of_at_most_14_days(): void
    {
        $this->fakeChanges();

        $exitCode = Artisan::call('tvchart:import:changes', [
            '--start' => '2026-07-30',
            '--end' => '2026-08-18',
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(['2026-07-30..2026-08-13', '2026-08-13..2026-08-18'], $this->sentWindows());
    }

    public function test_requests_a_single_window_for_a_short_range(): void
    {
        $this->fakeChanges();

        Artisan::call('tvchart:import:changes', [
            '--start' => '2026-08-10',
            '--end' => '2026-08-12',
        ]);

        $this->assertSame(['2026-08-10..2026-08-12'], $this->sentWindows());
    }

    public function test_imports_a_single_day_when_start_and_end_match(): void
    {
        $this->fakeChanges();

        Artisan::call('tvchart:import:changes', [
            '--start' => '2026-08-12',
            '--end' => '2026-08-12',
        ]);

        $this->assertSame(['2026-08-12..2026-08-12'], $this->sentWindows());
    }

    public function test_defaults_to_the_last_24_hours(): void
    {
        $this->fakeChanges();

        Artisan::call('tvchart:import:changes');

        $this->assertSame([now()->subDay()->format('Y-m-d').'..'.now()->format('Y-m-d')], $this->sentWindows());
    }

    public function test_dispatches_update_jobs_for_non_adult_shows(): void
    {
        $this->fakeChanges();

        Artisan::call('tvchart:import:changes', [
            '--start' => '2026-08-12',
            '--end' => '2026-08-12',
        ]);

        Queue::assertPushed(UpdateTvShow::class, 2);
    }

    public function test_follows_pagination_within_a_window(): void
    {
        Http::fake([
            'api.themoviedb.org/3/tv/changes*' => Http::sequence()
                ->push(['page' => 1, 'total_pages' => 2, 'total_results' => 3, 'results' => [
                    ['id' => 1, 'adult' => false],
                ]])
                ->push(['page' => 2, 'total_pages' => 2, 'total_results' => 3, 'results' => [
                    ['id' => 2, 'adult' => false],
                    ['id' => 3, 'adult' => false],
                ]]),
        ]);

        Artisan::call('tvchart:import:changes', [
            '--start' => '2026-08-12',
            '--end' => '2026-08-12',
        ]);

        Http::assertSentCount(2);
        Queue::assertPushed(UpdateTvShow::class, 3);
    }

    public function test_fails_on_an_unparseable_date(): void
    {
        $this->fakeChanges();

        $exitCode = Artisan::call('tvchart:import:changes', ['--start' => 'not-a-date']);

        $this->assertSame(1, $exitCode);
        Http::assertNothingSent();
    }

    public function test_fails_when_start_is_after_end(): void
    {
        $this->fakeChanges();

        $exitCode = Artisan::call('tvchart:import:changes', [
            '--start' => '2026-08-18',
            '--end' => '2026-07-30',
        ]);

        $this->assertSame(1, $exitCode);
        Http::assertNothingSent();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    private function fakeChanges(): void
    {
        Http::fake([
            'api.themoviedb.org/3/tv/changes*' => Http::response([
                'page' => 1,
                'total_pages' => 1,
                'total_results' => 3,
                'results' => [
                    ['id' => 1399, 'adult' => false],
                    ['id' => 1400, 'adult' => true],
                    ['id' => 1401, 'adult' => false],
                ],
            ]),
        ]);
    }

    /**
     * The `start..end` window of every changes request that was sent, in order.
     *
     * @return list<string>
     */
    private function sentWindows(): array
    {
        $windows = [];

        foreach (Http::recorded() as [$request]) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);
            $start = $query['start_date'] ?? null;
            $end = $query['end_date'] ?? null;
            $windows[] = (is_string($start) ? $start : '?').'..'.(is_string($end) ? $end : '?');
        }

        return array_values(array_unique($windows));
    }
}
