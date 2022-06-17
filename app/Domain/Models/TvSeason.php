<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
