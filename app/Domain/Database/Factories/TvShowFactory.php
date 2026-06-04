<?php declare(strict_types=1);

namespace App\Domain\Database\Factories;

use App\Domain\Enumerators\Status;
use App\Domain\Models\TvShow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TvShow>
 */
class TvShowFactory extends Factory
{
    protected $model = TvShow::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $firstAirDate = $this->faker->dateTimeBetween('-3 years', '+2 months');

        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'original_name' => $this->faker->sentence(3),
            'name' => $this->faker->sentence(3),
            'poster' => $this->faker->uuid().'.jpg',
            'overview' => $this->faker->paragraph(),
            'first_air_date' => $firstAirDate,
            'release_year' => (int) $firstAirDate->format('Y'),
            'primary_language' => 'en',
            'imdb_votes' => 0,
            'trakt_members' => 0,
            'status' => Status::UNREVIEWED,
        ];
    }

    /**
     * Aired a while ago without ever gaining traction.
     */
    public function stale(): self
    {
        return $this->state(fn () => [
            'first_air_date' => now()->subMonths(18),
            'imdb_votes' => 50,
            'trakt_members' => 50,
        ]);
    }

    /**
     * Matches the pending review queue criteria.
     */
    public function pendingReview(): self
    {
        return $this->state(fn () => [
            'status' => Status::UNREVIEWED,
            'flagged_for_review' => false,
            'first_air_date' => now()->addWeeks(2),
        ]);
    }

    /**
     * Popular show with high traction metrics.
     */
    public function popular(): self
    {
        return $this->state(fn () => [
            'imdb_votes' => 50_000,
            'trakt_members' => 50_000,
        ]);
    }
}
