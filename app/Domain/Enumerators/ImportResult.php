<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum ImportResult: string
{
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
