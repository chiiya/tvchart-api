<?php declare(strict_types=1);

namespace App\Domain\Services;

use Illuminate\Support\Facades\DB;

class TvShowService
{
    /**
     * Get a hashmap of all imdb ids known to us.
     */
    public function getImdbIds(): array
    {
        $dictionary = [];
        $results = DB::table('tv_shows')
            ->whereNotNull('imdb_id')
            ->select(['imdb_id'])
            ->get();

        foreach ($results as $result) {
            $dictionary[$result->imdb_id] = 0;
        }

        return $dictionary;
    }
}
