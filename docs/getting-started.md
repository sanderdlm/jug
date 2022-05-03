## Getting started

## Introduction

Each Jug project follows this structure:
```
|-- output/                        # Your generated site
|-- source/                        # Your site's source files
|  |-- _templates/                 # Templates, includes & macros. These files are not built as pages
|-- translations/                  # Optional: if you're building a multi-language site
|-- config.php                     # Configuration
|-- events.php                     # Optional: Pre and post build event handling
`-- composer.json                  # Dependencies
```
Jug expects two mandatory configuration options in your `config.php`: `source` and `output`. These are the two names of your source and output folders.

When you run the build or serve command, Jug will scan and build each Twig template inside the source folder and place the generated HTML in the output folder.

You can then [deploy](deploying.md) the content of the output folder to any host you like.

## Installation

There are two ways to start a new Jug project:
1. Require Jug through Composer and create all the required folders and files yourself.
```
mkdir my-new-project
cd my-new-project
composer require dreadnip/jug
mkdir output
mkdir source/_templates
touch config.php
vi config.php (enter correct configuration, see below)
./vendor/bin/jug serve
```
2. Use the skeleton project to get started more quickly
```
composer create-project dreadnip/jug-skeleton my-new-project
cd my-new-project
./vendor/bin/jug serve
```

## Commands

* `./vendor/bin/jug build` builds the site, once.
* `./vendor/bin/jug serve` launches the PHP built-in webserver and watches all files in the source folder for changes. The site will be rebuilt on every change.

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
