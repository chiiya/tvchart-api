<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\WatchProviderRegion.
 *
 * @property string $country
 * @property string $native_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|WatchProviderRegion newModelQuery()
 * @method static Builder|WatchProviderRegion newQuery()
 * @method static Builder|WatchProviderRegion query()
 *
 * @mixin \Eloquent
 */
class WatchProviderRegion extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'country';

    /** {@inheritDoc} */
    protected $keyType = 'string';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];
}
