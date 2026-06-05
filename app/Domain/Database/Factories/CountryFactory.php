<?php declare(strict_types=1);

namespace App\Domain\Database\Factories;

use App\Domain\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        return [
            'country_code' => $this->faker->unique()->countryCode(),
            'name' => $this->faker->country(),
            'native_name' => $this->faker->country(),
        ];
    }
}
