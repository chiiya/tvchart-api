<?php declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

class ShowIsAdultException extends Exception
{
    /** @var string */
    protected $message = 'TV show is marked as adult.';
}
