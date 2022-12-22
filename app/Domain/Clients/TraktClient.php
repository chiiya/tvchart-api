<?php declare(strict_types=1);

namespace App\Domain\Clients;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class TraktClient
{
    /**
     * Get extended show information for the given IMDB id from the Trakt API.
     *
     * @throws RequestException
     */
    public function getShowSummary(string $imdbId): array
    {
        $response = Http::asJson()
            ->withHeaders([
                'trakt-api-version' => 2,
                'trakt-api-key' => config('tv-chart.trakt.key'),
            ])
            ->get("https://api.trakt.tv/shows/{$imdbId}?extended=full");
        $response->throw();

        return $response->json();
    }

    /**
     * Get show member count for the given IMDB id from the Trakt API.
     *
     * @throws RequestException
     */
    public function getMemberCount(string $imdbId): int
    {
        $response = Http::asJson()
            ->withHeaders([
                'trakt-api-version' => 2,
                'trakt-api-key' => config('tv-chart.trakt.key'),
            ])
            ->get("https://api.trakt.tv/shows/{$imdbId}/stats");
        $response->throw();

        return (int) $response->json('collectors', 0);
    }

    /**
     * Get average season rating for the given IMDB id and season number from the Trakt API.
     *
     * @throws RequestException
     */
    public function getSeasonRating(string $imdbId, int $number): float
    {
        $response = Http::asJson()
            ->withHeaders([
                'trakt-api-version' => 2,
                'trakt-api-key' => config('tv-chart.trakt.key'),
            ])
            ->get("https://api.trakt.tv/shows/{$imdbId}/seasons/{$number}/ratings");
        $response->throw();

        return (float) $response->json('rating', 0);
    }
}
