<?php declare(strict_types=1);

namespace App\Application\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * {@inheritDoc}
     */
    public function hosts(): array
    {
        return [$this->allSubdomainsOfApplicationUrl()];
    }
}
