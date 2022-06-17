<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
