<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Download URL TTL (seconds)
    |--------------------------------------------------------------------------
    |
    | How long a pre-signed S3 download URL remains valid after generation.
    | Keep this short — the URL grants unauthenticated access to the epub file.
    | Default: 300 seconds (5 minutes). Set via DOWNLOAD_URL_TTL env variable.
    |
    */

    'download_url_ttl' => (int) env('DOWNLOAD_URL_TTL', 300),

    'formats' => [
        'conversion_matrix' => [
            'docx' => ['epub', 'fb2'],
            'epub' => ['fb2'],
            'fb2' => ['epub'],
        ],

        'converter_preference' => [
            'docx_to_epub' => 'pandoc',
            'docx_to_fb2' => 'calibre',
            'epub_to_fb2' => 'calibre',
            'fb2_to_epub' => 'calibre',
        ],
    ],

];
