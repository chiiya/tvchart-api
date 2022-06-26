<?php declare(strict_types=1);

namespace App\Domain\DTOs;

use App\Domain\Enumerators\ImportType;
use Chiiya\Common\Entities\DownloadedFile;
use Illuminate\Database\Eloquent\Model;
use Spatie\DataTransferObject\DataTransferObject;

class TmdbExport extends DataTransferObject
{
    public ImportType $type;
    public string $filename;
    public string $strategy;
    public Model $model;
    public array $ids = [];
    public ?DownloadedFile $file;
}
