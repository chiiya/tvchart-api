<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use App\Domain\Enumerators\ImportType;
use Chiiya\Common\Entities\DownloadedFile;
use Illuminate\Database\Eloquent\Model;

class TmdbExport
{
    public function __construct(
        public ImportType $type,
        public string $filename,
        public string $strategy,
        public Model $model,
        public ?DownloadedFile $file = null,
        public array $ids = [],
    ) {}
}
