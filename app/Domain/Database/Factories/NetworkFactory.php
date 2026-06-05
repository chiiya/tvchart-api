<?php declare(strict_types=1);

namespace App\Domain\Database\Factories;

use App\Domain\Models\Network;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Network>
 */
class NetworkFactory extends Factory
{
    protected $model = Network::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->numberBetween(1, 10_000_000),
            'name' => $this->faker->company(),
        ];
    }
}
