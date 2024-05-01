<?php declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Models\TvShow;
use App\Domain\Services\TvShowService;
use Chiiya\Common\Services\FileDownloader;

class ImdbClient
{
    public function __construct(
        private readonly FileDownloader $downloader,
        private readonly TvShowService $service,
    ) {}

    /**
     * Update IMDB votes and ratings for all ids known to us.
     */
    public function updateRatings(): void
    {
        $ids = $this->service->getImdbIds();
        $file = $this->downloader->download('https://datasets.imdbws.com/title.ratings.tsv.gz');
        $pointer = gzopen($file->getPath(), 'r');

        while ($line = gzgets($pointer)) {
            $data = str_getcsv($line, "\t");

            if (isset($ids[$data[0]])) {
                [$id, $rating, $votes] = $data;
                TvShow::query()->where('imdb_id', '=', $id)->update([
                    'imdb_score' => $rating,
                    'imdb_votes' => $votes,
                ]);
            }
        }

        gzclose($pointer);
        $file->delete();
    }
}
