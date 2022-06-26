<?php declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

class InsufficientDataException extends Exception
{
    /** @var string */
    protected $message = 'TV shows require at least an ID and a name.';
}
