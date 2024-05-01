<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\WatchProvider.
 *
 * @property int $tmdb_id
 * @property string $name
 * @property string $logo
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|WatchProvider newModelQuery()
 * @method static Builder|WatchProvider newQuery()
 * @method static Builder|WatchProvider query()
 *
 * @mixin \Eloquent
 */
class WatchProvider extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Is the watch provider whitelisted?
     */
    public function isWhitelisted(): bool
    {
        $providers = config('tv-chart.whitelist.providers');

        return (bool) (in_array($this->name, $providers, true));
    }
}
