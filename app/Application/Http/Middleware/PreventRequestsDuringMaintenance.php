<?php declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /** {@inheritDoc} */
    protected $except = [];
}
