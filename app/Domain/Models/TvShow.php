<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvShow extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];
    protected $casts = [
        'adult' => 'boolean',
        'first_air_date' => 'immutable_date',
        'imdb_score' => 'float',
        'locked_fields' => 'array',
    ];

    /**
     * Many-To-Many: One TV show has many production companies.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_tv_show', 'tv_show_id', 'company_id');
    }

    /**
     * Many-To-Many: One TV show has many production/origin countries.
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_tv_show', 'tv_show_id', 'country_code');
    }

    /**
     * Many-To-Many: One TV show has many spoken/origin languages.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_tv_show', 'tv_show_id', 'language_code');
    }

    /**
     * Many-To-Many: One TV show has many genres.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_tv_show', 'tv_show_id', 'genre_id');
    }

    /**
     * Many-To-Many: One TV show has many networks.
     */
    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(Network::class, 'network_tv_show', 'tv_show_id', 'network_id');
    }

    /**
     * Many-To-Many: One TV show has many watch providers, grouped by region.
     */
    public function watchProviders(): BelongsToMany
    {
        return $this->belongsToMany(
            WatchProvider::class,
            'tv_show_watch_provider',
            'tv_show_id',
            'watch_provider_id',
        )->withPivot(['region']);
    }

    /**
     * One-To-Many: One TV show has many TV seasons.
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(TvSeason::class, 'tv_show_id');
    }
}
