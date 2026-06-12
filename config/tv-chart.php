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

    /*
    |--------------------------------------------------------------------------
    | Facets
    |--------------------------------------------------------------------------
    |
    | Curated filter vocabularies for the frontend. The full set of networks in
    | the database is too long to be useful as a filter, so we expose only the
    | major streaming services and the top US cable networks, in priority order.
    | Names must match the network names as stored in the database.
    |
    */
    'facets' => [
        'networks' => [
            'Netflix',
            'Prime Video',
            'Apple TV',
            'Disney+',
            'Hulu',
            'Max',
            'Paramount+',
            'Peacock',
            'HBO',
            'Showtime',
            'FX',
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
    | Whitelist
    |--------------------------------------------------------------------------
    |
    | List of whitelisted (i.e. generally high-quality) entries.
    |
    */
    'whitelist' => [
        'networks' => [
            '/^Netflix$/',
            '/^Amazon$/',
            '/^Prime Video/',
            '/^Hulu$/',
            '/^Apple TV\+$/',
            '/^Disney\+$/',
            '/^Paramount\+$/',
            '/^Peacock$/',
            '/^BBC One$/',
            '/^Syfy$/',
            '/^Showtime$/',
            '/^HBO/',
            '/^Max/',
            '/^AMC/',
        ],
        'providers' => [
            'Netflix',
            'Amazon Prime Video',
            'Apple TV Plus',
            'Disney Plus',
            'Paramount Plus',
            'Hulu',
            'HBO Max',
            'HBO Go',
            'Sky Go',
            'Peacock',
        ],
        'countries' => ['US', 'GB'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist
    |--------------------------------------------------------------------------
    |
    | List of blacklisted entries.
    |
    */
    'blacklist' => [
        'genres' => ['Anime', 'Kids', 'News', 'Reality', 'Soap', 'Sport'],
        'networks' => ['Investigation Discovery', 'Oxygen', 'Disney Junior', 'Disney XD', 'Disney Channel'],
        // Not truly blacklisted, but only included when available on an international network
        'languages' => ['hi', 'te', 'zh', 'cn', 'th', 'ar', 'tr', 'tl', 'ja', 'ru'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stale Shows
    |--------------------------------------------------------------------------
    |
    | Shows that aired a while ago without ever gaining traction on IMDB or
    | Trakt are automatically blacklisted. The daily `tvchart:flag` command
    | acts as a safety net by re-flagging blacklisted shows that become
    | popular later on.
    |
    */
    'stale' => [
        'aired_before_months' => 12,
        'max_imdb_votes' => 1000,
        'max_trakt_members' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Digest
    |--------------------------------------------------------------------------
    |
    | Weekly email digest about shows pending manual review. When no
    | recipients are configured, all Filament users receive the digest.
    |
    */
    'digest' => [
        'recipients' => array_filter(explode(',', (string) env('DIGEST_RECIPIENTS', ''))),
        'limit' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | International Networks
    |--------------------------------------------------------------------------
    |
    | Networks where content is generally available in multiple languages
    | internationally.
    |
    */
    'international_networks' => ['Netflix', 'Amazon', 'Prime Video', 'Disney+', 'Apple TV+', 'Paramount+'],

    'countries' => [
        'KR' => 'South Korea',
    ],

    'archive_start' => '2022-01-01',
];
