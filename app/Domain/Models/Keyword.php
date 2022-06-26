<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Keyword.
 *
 * @property int $tmdb_id
 * @property string $name
 *
 * @method static Builder|Keyword newModelQuery()
 * @method static Builder|Keyword newQuery()
 * @method static Builder|Keyword query()
 * @mixin \Eloquent
 */
class Keyword extends Model
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
