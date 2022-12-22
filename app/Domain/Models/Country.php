<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Country.
 *
 * @property string $country_code
 * @property string $name
 * @property string $native_name
 *
 * @method static Builder|Country newModelQuery()
 * @method static Builder|Country newQuery()
 * @method static Builder|Country query()
 *
 * @mixin \Eloquent
 */
class Country extends Model
{
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
}
