<?php declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

class ImportAlreadyProcessedException extends Exception
{
    /** @var string */
    protected $message = 'The import file has already been processed.';
}
