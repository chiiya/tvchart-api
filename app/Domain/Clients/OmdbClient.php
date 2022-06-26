<?php declare(strict_types=1);

namespace App\Domain\Clients;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class OmdbClient
{
    /**
     * Get show details for the given IMDB id from the OMDB API.
     *
     * @throws RequestException
     */
    public function getShow(string $imdbId): array
    {
        $response = Http::get('https://www.omdbapi.com?'.http_build_query([
            'apikey' => config('tv-chart.omdb.key'),
            'i' => $imdbId,
            't' => 'series',
            'plot' => 'short',
        ]));
        $response->throw();

        return [
            'summary' => $response->json('Plot') ?: null,
            'genres' => explode(', ', $response->json('Genre')),
        ];
    }
}
