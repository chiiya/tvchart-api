<?php declare(strict_types=1);

namespace App\Domain\Strategies;

use App\Domain\DTOs\TmdbExport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DirectImportStrategy implements ImportStrategy
{
    public function execute(TmdbExport $data): void
    {
        DB::disableQueryLog();
        $this->processExport($data);
        DB::enableQueryLog();
    }

    /**
     * Process all lines of the TMDB export. Each line contains a JSON-encoded entity.
     * Items are updated in database in batches of 500 at a time using the given
     * $model.
     */
    protected function processExport(TmdbExport $data): void
    {
        $pointer = gzopen($data->file->getPath(), 'r');
        $items = [];

        while ($line = gzgets($pointer)) {
            $item = json_decode($line, true);
            $items[] = $item;
            $data->ids[$item['id']] = true;

            if (count($items) === 500) {
                $this->upsertItems($data->model, $items);
                $items = [];
            }
        }

        if (count($items) > 0) {
            $this->upsertItems($data->model, $items);
        }

        gzclose($pointer);
    }

    /**
     * Upsert up to 500 $items of the given $model.
     */
    protected function upsertItems(Model $model, array $items): void
    {
        $model->newQuery()->upsert(
            array_map(fn (array $item) => [
                $model->getKeyName() => $item['id'],
                'name' => $item['name'],
            ], $items),
            [$model->getKeyName()],
            ['name'],
        );
    }
}
