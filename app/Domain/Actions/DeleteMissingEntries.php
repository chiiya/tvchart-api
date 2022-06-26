<?php declare(strict_types=1);

namespace App\Domain\Actions;

use App\Domain\DTOs\TmdbExport;
use Closure;
use Illuminate\Support\Facades\DB;

class DeleteMissingEntries
{
    /**
     * Delete all entries no longer present in the new export.
     */
    public function handle(TmdbExport $data, Closure $next): mixed
    {
        $oldIds = DB::table($data->model->getTable())
            ->select($data->model->getKeyName())
            ->pluck($data->model->getKeyName(), $data->model->getKeyName())
            ->all();
        $missing = array_diff_key($oldIds, $data->ids);
        $data->model->newQuery()->whereIn($data->model->getKeyName(), $missing)->delete();

        return $next($data);
    }
}
