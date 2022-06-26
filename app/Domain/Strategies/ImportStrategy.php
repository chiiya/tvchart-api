<?php declare(strict_types=1);

namespace App\Domain\Strategies;

use App\Domain\DTOs\TmdbExport;

interface ImportStrategy
{
    public function execute(TmdbExport $data): void;
}
