<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Database\Factories\TvShowFactory;
use App\Domain\Enumerators\BlacklistReason;
use App\Domain\Enumerators\Status;
use App\Domain\Presenters\TvShowPresenter;
use Carbon\CarbonImmutable;
use Chiiya\Common\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property string|null $imdb_id
 * @property int|null $tvdb_id
 * @property CarbonImmutable|null $trakt_synced_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Status $status
 * @property bool $flagged_for_review
 * @property CarbonImmutable|null $status_updated_at
 * @property BlacklistReason|null $blacklist_reason
 * @property Collection<int, Company> $companies
 * @property int|null $companies_count
 * @property Collection<int, Country> $countries
 * @property int|null $countries_count
 * @property Collection<int, Genre> $genres
 * @property int|null $genres_count
 * @property Collection<int, Language> $languages
 * @property int|null $languages_count
 * @property Collection<int, Network> $networks
 * @property int|null $networks_count
 * @property Collection<int, TvSeason> $seasons
 * @property int|null $seasons_count
 * @property Collection<int, WatchProvider> $watchProviders
 * @property int|null $watch_providers_count
 *
 * @method static Builder<static>|TvShow newModelQuery()
 * @method static Builder<static>|TvShow newQuery()
 * @method static Builder<static>|TvShow query()
 * @method static Builder<static>|TvShow pendingReview()
 *
 * @mixin \Eloquent
 */
class TvShow extends Model
{
    /** @use HasFactory<TvShowFactory> */
    use HasFactory;

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
        'status_updated_at' => 'immutable_date',
        'trakt_synced_at' => 'immutable_datetime',
        'imdb_score' => 'float',
        'status' => Status::class,
        'flagged_for_review' => 'boolean',
        'trakt_members' => 'integer',
        'blacklist_reason' => BlacklistReason::class,
    ];

    /** {@inheritDoc} */
    protected $attributes = [
        'status' => 0,
    ];
    protected string $presenter = TvShowPresenter::class;

    protected static function newFactory(): TvShowFactory
    {
        return TvShowFactory::new();
    }

    /**
     * Many-To-Many: One TV show has many production companies.
     *
     * @return BelongsToMany<Company, $this>
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_tv_show', 'tv_show_id', 'company_id');
    }

    /**
     * Many-To-Many: One TV show has many production/origin countries.
     *
     * @return BelongsToMany<Country, $this>
     */
    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'country_tv_show', 'tv_show_id', 'country_code');
    }

    /**
     * Many-To-Many: One TV show has many spoken/origin languages.
     *
     * @return BelongsToMany<Language, $this>
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'language_tv_show', 'tv_show_id', 'language_code');
    }

    /**
     * Many-To-Many: One TV show has many genres.
     *
     * @return BelongsToMany<Genre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'genre_tv_show', 'tv_show_id', 'genre_id');
    }

    /**
     * Many-To-Many: One TV show has many networks.
     *
     * @return BelongsToMany<Network, $this>
     */
    public function networks(): BelongsToMany
    {
        return $this->belongsToMany(Network::class, 'network_tv_show', 'tv_show_id', 'network_id');
    }

    /**
     * Many-To-Many: One TV show has many watch providers, grouped by region.
     *
     * @return BelongsToMany<WatchProvider, $this>
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
     * @return HasMany<TvSeason, $this>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(TvSeason::class, 'tv_show_id');
    }

    /**
     * Scope: Shows pending manual review, ordered by review priority:
     * flagged shows first, then by how close the show's latest season air
     * date is to today (so currently-airing and soon-airing shows surface
     * before older ones), with popularity as a tiebreaker.
     *
     * Review priority is based on the latest season's air date, not the
     * show's premiere date: a long-running show that premiered years ago but
     * has a new season airing now is highly relevant, while a show whose last
     * season aired before the archive start is never displayed and is
     * excluded. Seasons airing further out than the horizon are ignored so
     * that a returning show is judged by its nearest relevant season rather
     * than a far-off announced one.
     *
     * @param Builder<TvShow> $query
     *
     * @return Builder<TvShow>
     */
    public function scopePendingReview(Builder $query): Builder
    {
        $today = now()->toDateString();
        $horizon = now()->addMonths(2)->toDateString();

        $latestSeason = TvSeason::query()
            ->selectRaw('tv_show_id, MAX(first_air_date) AS last_air_date')
            ->where('number', '>', 0)
            ->whereNotNull('first_air_date')
            ->where('first_air_date', '<', $horizon)
            ->groupBy('tv_show_id');

        // Day distance between the latest season air date and today. Postgres
        // subtracts dates directly; SQLite (used in tests) needs julianday().
        $proximity = $this->getConnection()->getDriverName() === 'sqlite'
            ? 'ABS(julianday(latest_season.last_air_date) - julianday(?))'
            : 'ABS(latest_season.last_air_date - ?::date)';

        return $query
            ->select('tv_shows.*')
            ->joinSub($latestSeason, 'latest_season', 'latest_season.tv_show_id', '=', 'tv_shows.tmdb_id')
            ->where(
                fn (Builder $builder) => $builder
                    ->where('tv_shows.status', '=', Status::UNREVIEWED)
                    ->orWhere('tv_shows.flagged_for_review', '=', true),
            )
            ->whereNotNull('tv_shows.poster')
            ->whereNotNull('tv_shows.overview')
            ->has('genres')
            ->has('networks')
            ->where('latest_season.last_air_date', '>=', config('tv-chart.archive_start'))
            ->orderByRaw($proximity, [$today])
            ->orderByDesc('tv_shows.trakt_members')
            ->orderByDesc('tv_shows.imdb_votes');
    }
}
