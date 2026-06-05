<?php declare(strict_types=1);

namespace App\Domain\Tests\Feature\Actions;

use App\Domain\Actions\TvShows\UpdateStatus;
use App\Domain\DTOs\UpdateTvShowData;
use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Models\Network;
use App\Domain\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_flags_unavailable_show_that_became_available(): void
    {
        $show = $this->createForeignShow(Status::BLACKLISTED, onNetflix: true);

        $this->runAction($show);

        $this->assertTrue($show->refresh()->flagged_for_review);
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $show->tmdb_id,
            'description' => 'Flagged for review: became internationally available.',
        ]);
    }

    public function test_does_not_flag_show_that_is_still_unavailable(): void
    {
        $show = $this->createForeignShow(Status::BLACKLISTED, onNetflix: false);

        $this->runAction($show);

        $this->assertFalse($show->refresh()->flagged_for_review);
    }

    public function test_does_not_flag_finally_blacklisted_show(): void
    {
        $show = $this->createForeignShow(Status::BLACKLISTED_FINAL, onNetflix: true);

        $this->runAction($show);

        $this->assertFalse($show->refresh()->flagged_for_review);
    }

    public function test_does_not_flag_show_blacklisted_for_other_reasons(): void
    {
        $show = $this->createForeignShow(Status::BLACKLISTED, onNetflix: true, reason: BlacklistReason::NO_TRACTION);

        $this->runAction($show);

        $this->assertFalse($show->refresh()->flagged_for_review);
    }

    public function test_does_not_log_for_already_flagged_show(): void
    {
        $show = $this->createForeignShow(Status::BLACKLISTED, onNetflix: true);
        $show->update(['flagged_for_review' => true]);

        $this->runAction($show);

        $this->assertDatabaseMissing('activity_log', [
            'subject_id' => $show->tmdb_id,
            'description' => 'Flagged for review: became internationally available.',
        ]);
    }

    public function test_evaluates_unreviewed_show(): void
    {
        $show = $this->createForeignShow(Status::UNREVIEWED, onNetflix: false, reason: null);

        $this->runAction($show);

        $this->assertSame(Status::BLACKLISTED, $show->refresh()->status);
        $this->assertSame(BlacklistReason::UNAVAILABLE, $show->blacklist_reason);
    }

    private function createForeignShow(
        Status $status,
        bool $onNetflix,
        ?BlacklistReason $reason = BlacklistReason::UNAVAILABLE,
    ): TvShow {
        $show = TvShow::factory()->foreign()->create([
            'status' => $status,
            'blacklist_reason' => $reason,
            'first_air_date' => now()->subMonths(2),
        ]);

        if ($onNetflix) {
            $network = Network::factory()->create([
                'name' => 'Netflix',
            ]);
            $show->networks()->attach($network);
        }

        return $show;
    }

    private function runAction(TvShow $show): void
    {
        resolve(UpdateStatus::class)->handle(
            new UpdateTvShowData(id: $show->tmdb_id, show: $show),
            fn (UpdateTvShowData $data) => $data,
        );
    }
}
