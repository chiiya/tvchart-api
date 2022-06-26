<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Models\Language.
 *
 * @property string $language_code
 * @property string $name
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @mixin \Eloquent
 */
class Language extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    public $timestamps = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'language_code';

    /** {@inheritDoc} */
    protected $keyType = 'string';

    /** {@inheritDoc} */
    protected $guarded = [];
}
