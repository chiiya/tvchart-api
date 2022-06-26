<?php declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

class EntityDeletedException extends Exception
{
    /** @var string */
    protected $message = 'The requested entity was not found on TMDB.';
}
