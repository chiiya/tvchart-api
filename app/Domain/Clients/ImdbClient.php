<?php declare(strict_types=1);

namespace App\Domain\Clients;

use Chiiya\Common\Services\FileDownloader;
use Illuminate\Support\Facades\DB;

readonly class ImdbClient
{
    public function __construct(
        private FileDownloader $downloader,
    ) {}

    /**
     * Update IMDB votes and ratings for all ids known to us.
     */
    public function updateRatings(): void
    {
        DB::disableQueryLog();
        $file = $this->downloader->download('https://datasets.imdbws.com/title.ratings.tsv.gz');
        $this->createTemporaryTable();
        /** @var resource $pointer */
        $pointer = gzopen($file->getPath(), 'r');
        $shows = [];
        $count = 0;

        while ($line = gzgets($pointer)) {
            $data = str_getcsv($line, "\t");
            $shows[] = [
                'imdb_id' => $data[0],
                'rating' => (float) $data[1],
                'votes' => (int) $data[2],
            ];
            ++$count;

            if ($count === 1000) {
                DB::table('tmp')->insert($shows);
                $count = 0;
                $shows = [];
            }
        }

        if ($count > 0) {
            DB::table('tmp')->insert($shows);
        }

        gzclose($pointer);
        $file->delete();
        $this->updateTvShows();
        DB::enableQueryLog();
    }

    private function createTemporaryTable(): void
    {
        DB::statement('DROP TABLE IF EXISTS tmp');
        DB::statement('CREATE TABLE tmp (imdb_id VARCHAR(10) PRIMARY KEY, rating NUMERIC(9,2), votes BIGINT)');
    }

    private function updateTvShows(): void
    {
        DB::statement('
            UPDATE tv_shows AS t
            SET imdb_score = tmp.rating, imdb_votes = tmp.votes
            FROM tmp
            WHERE t.imdb_id = tmp.imdb_id
        ');
        DB::statement('DROP TABLE IF EXISTS tmp');
    }
}
