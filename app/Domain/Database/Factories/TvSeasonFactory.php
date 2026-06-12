<?php declare(strict_types=1);

namespace App\Domain\Database\Factories;

use App\Domain\Models\TvSeason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TvSeason>
 */
class TvSeasonFactory extends Factory
{
    protected $model = TvSeason::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'number' => 1,
            'name' => $this->faker->sentence(2),
            'overview' => $this->faker->paragraph(),
            'first_air_date' => $this->faker->dateTimeBetween('-3 years', '+2 months'),
        ];
    }
}
