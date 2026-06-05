<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Country.
 *
 * @property string $country_code
 * @property string $name
 * @property string $native_name
 *
 * @method static Builder<static>|Country newModelQuery()
 * @method static Builder<static>|Country newQuery()
 * @method static Builder<static>|Country query()
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;

    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    public $timestamps = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'country_code';

    /** {@inheritDoc} */
    protected $keyType = 'string';

    /** {@inheritDoc} */
    protected $guarded = [];

    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }

    /**
     * Is the country whitelisted?
     */
    public function isWhitelisted(): bool
    {
        $countries = config('tv-chart.whitelist.countries');

        return in_array($this->country_code, $countries, true);
    }
}
