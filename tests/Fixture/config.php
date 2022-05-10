<?php

return [
    'source' => 'tests/Fixture/source',
    'output' => 'tests/Fixture/output',
    'default_locale' => 'en',
    'locales' => [
        'en',
        'fr'
    ],
    'hash' => bin2hex(random_bytes(4)),
    'year' => (new DateTime('now'))->format('Y'),
    'image_cache' => 'images.json',
];
