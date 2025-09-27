<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Genre.
 *
 * @property int $id
 * @property string $name
 *
 * @method static Builder<static>|Genre newModelQuery()
 * @method static Builder<static>|Genre newQuery()
 * @method static Builder<static>|Genre query()
 *
 * @mixin \Eloquent
 */
class Genre extends Model
{
    /** {@inheritDoc} */
    public $timestamps = false;

    /** {@inheritDoc} */
    protected $guarded = ['id'];
}
