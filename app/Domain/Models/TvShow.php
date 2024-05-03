<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Enumerators\Status;
use App\Domain\Presenters\TvShowPresenter;
use Carbon\CarbonImmutable;
use Chiiya\Common\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\TvShow.
 *
 * @property int $tmdb_id
 * @property string $original_name
 * @property string|null $name
 * @property int|null $runtime
 * @property string|null $backdrop
 * @property string|null $poster
 * @property CarbonImmutable|null $first_air_date
 * @property int|null $release_year
 * @property string|null $summary
 * @property string|null $overview
 * @property string|null $production_status
 * @property string|null $type
 * @property string|null $primary_language
 * @property string|null $content_rating
 * @property float $imdb_score
 * @property int $imdb_votes
 * @property int $trakt_members
 * @property float|null $popularity
 * @property string|null $imdb_id
 * @property int|null $tvdb_id
 * @property array|null $locked_fields
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Status $status
 * @property bool $flagged_for_review
 * @property Collection|Company[] $companies
 * @property int|null $companies_count
 * @property Collection|Country[] $countries
 * @property int|null $countries_count
 * @property Collection|Genre[] $genres
 * @property int|null $genres_count
 * @property Collection|Language[] $languages
 * @property int|null $languages_count
 * @property Collection|Network[] $networks
 * @property int|null $networks_count
 * @property Collection|TvSeason[] $seasons
 * @property int|null $seasons_count
 * @property Collection|WatchProvider[] $watchProviders
 * @property int|null $watch_providers_count
 *
 * @method static Builder|TvShow newModelQuery()
 * @method static Builder|TvShow newQuery()
 * @method static Builder|TvShow query()
 *
 * @mixin \Eloquent
 */
class TvShow extends Model
{
    /** @use PresentableTrait<TvShowPresenter> */
    use PresentableTrait;

    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = ['created_at', 'updated_at'];

    /** {@inheritDoc} */
    protected $casts = [
        'first_air_date' => 'immutable_date',
        'imdb_score' => 'float',
        'locked_fields' => 'array',
        'status' => Status::class,
        'flagged_for_review' => 'boolean',
        'trakt_members' => 'integer',
    ];

    /** {@inheritDoc} */
    protected $attributes = [
        'status' => 0,
    ];
    protected string $presenter = TvShowPresenter::class;

    /**
     * Many-To-Many: One TV show has many production companies.
     *
     * @return BelongsToMany<Company>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_tv_show', 'tv_show_id', 'company_id');
    }

    /**
     * Many-To-Many: One TV show has many production/origin countries.
     *
     * @return BelongsToMany<Country>
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_tv_show', 'tv_show_id', 'country_code');
    }

    /**
     * Many-To-Many: One TV show has many spoken/origin languages.
     *
     * @return BelongsToMany<Language>
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_tv_show', 'tv_show_id', 'language_code');
    }

    /**
     * Many-To-Many: One TV show has many genres.
     *
     * @return BelongsToMany<Genre>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_tv_show', 'tv_show_id', 'genre_id');
    }

    /**
     * Many-To-Many: One TV show has many networks.
     *
     * @return BelongsToMany<Network>
     */
    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(Network::class, 'network_tv_show', 'tv_show_id', 'network_id');
    }

    /**
     * Many-To-Many: One TV show has many watch providers, grouped by region.
     *
     * @return BelongsToMany<WatchProvider>
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
     *
     * @return HasMany<TvSeason>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(TvSeason::class, 'tv_show_id');
    }
}
