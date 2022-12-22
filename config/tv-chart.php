<?php declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    |
    | Configuration for the admin.
    |
    */
    'admin' => [
        'password' => env('ADMIN_DEFAULT_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Certifications
    |--------------------------------------------------------------------------
    |
    | List of all US TV ratings from TMDB.
    |
    */
    'certifications' => ['NR', 'TV-Y', 'TV-Y7', 'TV-G', 'TV-PG', 'TV-14', 'TV-MA'],

    /*
    |--------------------------------------------------------------------------
    | Genre mappings
    |--------------------------------------------------------------------------
    |
    | Genre mappings for each service / API. Only genres that are of interest
    | to use are included, e.g. for TMDB we're not including some ambiguous ones
    | like `Sci-Fi & Fantasy` since we store those separately in our database.
    |
    */
    'genres' => [
        'tmdb' => [
            'Animation' => 'Animation',
            'Comedy' => 'Comedy',
            'Crime' => 'Crime',
            'Documentary' => 'Documentary',
            'Drama' => 'Drama',
            'Family' => 'Family',
            'Kids' => 'Kids',
            'Mystery' => 'Mystery',
            'News' => 'News',
            'Reality' => 'Reality',
            'Soap' => 'Soap',
            'Talk' => 'Talk Show',
        ],
        'tvdb' => [
            'Action' => 'Action',
            'Adventure' => 'Adventure',
            'Animation' => 'Animation',
            'Anime' => 'Anime',
            'Children' => 'Kids',
            'Comedy' => 'Comedy',
            'Crime' => 'Crime',
            'Documentary' => 'Documentary',
            'Drama' => 'Drama',
            'Family' => 'Family',
            'Fantasy' => 'Fantasy',
            //            'Food' => 'Food',
            'Game Show' => 'Game Show',
            'History' => 'History',
            'Horror' => 'Horror',
            'Mystery' => 'Mystery',
            'News' => 'News',
            'Reality' => 'Reality',
            'Romance' => 'Romance',
            'Science Fiction' => 'Science Fiction',
            'Soap' => 'Soap',
            'Talk Show' => 'Talk Show',
            //            'Travel' => 'Travel',
        ],
        'omdb' => [
            'Action' => 'Action',
            'Adventure' => 'Adventure',
            'Animation' => 'Animation',
            'Comedy' => 'Comedy',
            'Crime' => 'Crime',
            'Documentary' => 'Documentary',
            'Drama' => 'Drama',
            'Family' => 'Family',
            'Fantasy' => 'Fantasy',
            'Game Show' => 'Game Show',
            'History' => 'History',
            'Horror' => 'Horror',
            'Mystery' => 'Mystery',
            'News' => 'News',
            'Reality-TV' => 'Reality',
            'Romance' => 'Romance',
            'Sci-Fi' => 'Science Fiction',
            'Talk Show' => 'Talk Show',
        ],
        'trakt' => [
            'action' => 'Action',
            'adventure' => 'Adventure',
            'animation' => 'Animation',
            'anime' => 'Anime',
            'children' => 'Kids',
            'comedy' => 'Comedy',
            'crime' => 'Crime',
            'documentary' => 'Documentary',
            'drama' => 'Drama',
            'family' => 'Family',
            'fantasy' => 'Fantasy',
            'game-show' => 'Game Show',
            'history' => 'History',
            'horror' => 'Horror',
            'mystery' => 'Mystery',
            'news' => 'News',
            'reality' => 'Reality',
            'romance' => 'Romance',
            'science-fiction' => 'Science Fiction',
            'soap' => 'Soap',
            'sporting-event' => 'Sport',
            'talk-show' => 'Talk Show',
        ],
    ],

    'tmdb' => [
        'exports' => 'https://files.tmdb.org/p/exports/',
    ],

    'omdb' => [
        'key' => env('OMDB_API_TOKEN'),
    ],

    'trakt' => [
        'key' => env('TRAKT_API_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Seasons
    |--------------------------------------------------------------------------
    |
    | Mapping of start month to season name.
    |
    */
    'seasons' => [
        1 => 'winter',
        4 => 'spring',
        7 => 'summer',
        10 => 'fall',
    ],
];
