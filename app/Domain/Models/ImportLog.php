<?php declare(strict_types=1);

namespace App\Domain\Models;

use App\Domain\Enumerators\ImportResult;
use App\Domain\Enumerators\ImportStatus;
use App\Domain\Enumerators\ImportType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Domain\Models\ImportLog.
 *
 * @property int $id
 * @property string|null $filename
 * @property ImportStatus $status
 * @property ImportResult|null $result
 * @property ImportType $import_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ImportLog newModelQuery()
 * @method static Builder|ImportLog newQuery()
 * @method static Builder|ImportLog query()
 *
 * @mixin \Eloquent
 */
class ImportLog extends Model
{
    /** {@inheritDoc} */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /** {@inheritDoc} */
    protected $casts = [
        'result' => ImportResult::class,
        'status' => ImportStatus::class,
        'import_type' => ImportType::class,
    ];
}
