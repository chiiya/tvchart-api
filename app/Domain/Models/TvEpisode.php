<?php declare(strict_types=1);

namespace App\Domain\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\TvEpisode.
 *
 * @property int $tmdb_id
 * @property int $number
 * @property string|null $name
 * @property CarbonImmutable|null $first_air_date
 * @property string|null $overview
 * @property int|null $runtime
 * @property string|null $still
 * @property int $tv_season_id
 * @property int|null $tvdb_id
 * @property array|null $locked_fields
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property TvSeason $show
 *
 * @method static Builder|TvEpisode newModelQuery()
 * @method static Builder|TvEpisode newQuery()
 * @method static Builder|TvEpisode query()
 * @mixin \Eloquent
 */
class TvEpisode extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];
    protected $casts = [
        'first_air_date' => 'immutable_date',
        'locked_fields' => 'array',
    ];

    /**
     * One-To-Many: One TV episode belongs to one TV season.
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(TvSeason::class, 'tv_season_id');
    }
}
