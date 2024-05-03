<?php declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /** {@inheritDoc} */
    protected $except = [];
}
