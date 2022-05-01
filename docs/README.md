# Jug documentation

## Introduction

## Installation

There are two ways to start a new Jug project:
1. You can either create a folder yourself, require Jug through Composer and create all the required folders and files yourself.
```
mkdir my-new-project
cd my-new-project
composer require dreadnip/jug
mkdir source/_templates
touch config.php
```
2. You can use the skeleton project to get started more quickly
```
composer create-project dreadnip/jug-skeleton
```

## Commands

* `./vendor/bin/jug build` builds the site.
* `./vendor/bin/jug serve` launches the PHP built-in webserver and watches all file sin the source folder for changes. The site will be rebuilt on every change.

## Configuration

All configuration is done through a PHP file, named config.php, in the root of your project. This file is mandatory. Your config file should return a single array with strings as keys. The following keys are required for Jug to function:
* source: the folder that has all of your site's source files
* output: the folder your site will be generated in
* default_locale: a string representing a language
* image_cache: path to a jso file to cache image paths when they're optimised

Example:
```php
<?php

return [
    'source' => 'source',
    'output' => 'output',
    'default_locale' => 'en',
    'hash' => bin2hex(random_bytes(4)),
    'year' => (new DateTime('now'))->format('Y'),
    'image_cache' => 'images.json',
];
```

## Translations

Translations are available using the `symfony/translations` package. In the context of Jug site you'll mostly end up using the `|trans` Twig filter.

## Optimized images

Jug will look for optipng and jpegoptim on your local system and both will be used on all images when you build your site. A local cache of which images have already been optimised is kept to prevent multiple runs.

Linux:
```
sudo apt install jpegoptim optipng
```
## Custom Twig filters and functions

### Functions

* `asset('some-asset.png)` will return a versioned filename of that asset, e.g. `some-asset-f8d7dk3.png`
* `markdown('this is *markdown*')` will render markdown.
* `dir('source')` will return an iterable list of all filenames in a folder. The key is a clean filename without extension, the value is the full path to the file.
* `highlight('$foo ? $bar : null')` will highlight some code using the PHP `highlight_string` function.
* `data('main.db', 'SELECT * FROM posts)` can be used to select data from a sqlite database.

### Filters

* markdown: same as the function but as a filter. Can be used directly after strings or with an apply block

`'some **markdown** content'|markdown`

Or
```
{% apply markdown %}
 # More markdown!
 
 This could be an entire blog post!
 
 * a list
 * more list items
{% endapply %}
```

## Events

There are two events that are thrown during the Jug build process: BeforeBuild and AfterBuild. You can create an `events.php` file in your project root to listen for these events:

```php
<?php

declare(strict_types=1);

use Jug\Event\AfterBuild;
use Jug\Event\BeforeBuild;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

return static function (EventDispatcher $dispatcher): void {
    $dispatcher->addListener(BeforeBuild::NAME, function (Event $event) {
        /** @var BeforeBuild $beforeBuild */
        $beforeBuild = $event;

    });

    $dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
        /** @var AfterBuild $afterBuild */
        $afterBuild = $event;
    });
};
```

