<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Company.
 *
 * @property int $tmdb_id
 * @property string $name
 *
 * @method static Builder|Company newModelQuery()
 * @method static Builder|Company newQuery()
 * @method static Builder|Company query()
 *
 * @mixin \Eloquent
 */
class Company extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    public $timestamps = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'tmdb_id';

    /** {@inheritDoc} */
    protected $guarded = [];
}
