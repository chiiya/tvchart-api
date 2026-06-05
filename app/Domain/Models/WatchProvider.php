<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Database\Factories\WatchProviderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @method static Builder<static>|WatchProvider newModelQuery()
 * @method static Builder<static>|WatchProvider newQuery()
 * @method static Builder<static>|WatchProvider query()
 *
 * @mixin \Eloquent
 */
class WatchProvider extends Model
{
    /** @use HasFactory<WatchProviderFactory> */
    use HasFactory;

    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];

    protected static function newFactory(): WatchProviderFactory
    {
        return WatchProviderFactory::new();
    }

    /**
     * Is the watch provider whitelisted?
     */
    public function isWhitelisted(): bool
    {
        $providers = config('tv-chart.whitelist.providers');

        return (bool) (in_array($this->name, $providers, true));
    }
}
