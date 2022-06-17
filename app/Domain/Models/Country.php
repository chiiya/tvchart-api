<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** {@inheritDoc} */
    public $incrementing = false;

    /** {@inheritDoc} */
    public $timestamps = false;

    /** {@inheritDoc} */
    protected $primaryKey = 'country_code';

    /** {@inheritDoc} */
    protected $keyType = 'string';

    /** {@inheritDoc} */
    protected $guarded = [];
}
