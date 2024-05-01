<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\Network.
 *
 * @property int $tmdb_id
 * @property string $name
 * @property string|null $logo
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Network newModelQuery()
 * @method static Builder|Network newQuery()
 * @method static Builder|Network query()
 *
 * @mixin \Eloquent
 */
class Network extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Is the network whitelisted?
     */
    public function isWhitelisted(): bool
    {
        $networks = config('tv-chart.whitelist.networks');

        foreach ($networks as $network) {
            if (preg_match($network, $this->name)) {
                return true;
            }
        }

        return false;
    }
}
