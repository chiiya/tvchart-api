<?php declare(strict_types=1);

namespace App\Domain\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\TvSeason.
 *
 * @property int $tmdb_id
 * @property int $tv_show_id
 * @property int $number
 * @property string|null $name
 * @property string|null $summary
 * @property string|null $poster
 * @property CarbonImmutable|null $first_air_date
 * @property int|null $release_year
 * @property int|null $tvdb_id
 * @property array|null $locked_fields
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|TvEpisode[] $episodes
 * @property int|null $episodes_count
 * @property TvShow $show
 *
 * @method static Builder|TvSeason newModelQuery()
 * @method static Builder|TvSeason newQuery()
 * @method static Builder|TvSeason query()
 * @mixin \Eloquent
 */
class TvSeason extends Model
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
     * One-To-Many: One TV season belongs to one TV show.
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(TvShow::class, 'tv_show_id');
    }

    /**
     * One-To-Many: One TV season has many TV episodes.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(TvEpisode::class, 'tv_season_id');
    }
}
