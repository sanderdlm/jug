<?php

return [
    'source' => __DIR__ . '/source',
    'output' => __DIR__ . '/output',
    'default_locale' => 'en',
    'locales' => [
        'en',
        'fr'
    ],
    'hash' => bin2hex(random_bytes(4)),
    'year' => (new DateTime('now'))->format('Y'),
    'image_cache' => __DIR__ . '/images.json',
];
