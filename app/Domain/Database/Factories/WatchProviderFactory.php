<?php declare(strict_types=1);

namespace App\Domain\Database\Factories;

use App\Domain\Models\WatchProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WatchProvider>
 */
class WatchProviderFactory extends Factory
{
    protected $model = WatchProvider::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'name' => $this->faker->company(),
            'logo' => $this->faker->uuid().'.jpg',
            'priority' => $this->faker->numberBetween(1, 100),
        ];
    }
}
