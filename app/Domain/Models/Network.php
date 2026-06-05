<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Database\Factories\NetworkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @method static Builder<static>|Network newModelQuery()
 * @method static Builder<static>|Network newQuery()
 * @method static Builder<static>|Network query()
 *
 * @mixin \Eloquent
 */
class Network extends Model
{
    /** @use HasFactory<NetworkFactory> */
    use HasFactory;

    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];

    protected static function newFactory(): NetworkFactory
    {
        return NetworkFactory::new();
    }

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
