<?php declare(strict_types=1);

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkMapping extends Model
{
    /** {@inheritDoc} */
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
