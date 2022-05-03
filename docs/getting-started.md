## Getting started

## Introduction

Each Jug project follows this structure:
```
|-- output/                        # Your generated site
|-- source/                        # Your site's source files
|  |-- _templates/                 # Templates, includes & macros. These files are not built as pages
|  `-- assets/                     # Images, stylesheets, scripts, ...
|-- translations/                  # Optional: if you're building a multi-language site
|-- config.php                     # Configuration
|-- events.php                     # Optional: Pre and post build event handling
`-- composer.json                  # Dependencies
```
When you run the build or serve command, Jug will scan and build each Twig template inside the source folder and dump the generated HTML in the output folder.

You can then [deploy](deploying.md) the content of the output folder to any host you like.

## Installation

To start a new Jug site, create your folder, require the package through Composer and run the `init` command.
```
mkdir my-new-site
cd my-new-site
composer require dreadnip/jug
./vendor/bin/jug init
```

## Commands

* `./vendor/bin/jug build` builds the site, once.
* `./vendor/bin/jug serve` launches the PHP built-in webserver and watches all files in the source folder for changes. The site will be rebuilt on every change.
* `./vendor/bin/jug init` generate a blank project skeleton.

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
