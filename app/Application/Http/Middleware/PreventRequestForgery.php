<?php declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;

class PreventRequestForgery extends Middleware
{
    /** {@inheritDoc} */
    protected $except = [];
}
