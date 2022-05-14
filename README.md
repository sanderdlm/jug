# Jug

Jug is a minimal static site generator that turns Twig templates into HTML.

It uses Twig and other Symfony components, so it should feel familiar for people with Symfony experience.

## Features

* Twig-powered
* Multilang ready (symfony/translations)
* Optimized images
* Watch & serve command for quick development
* Easy, PHP based config
* Events to customize the build before & after

## Getting started

Make a new project directory, install the package using composer and run the init command.
```
composer require dreadnip/jug
./vendor/bin/jug init
```
You'll end up with a simple, one-page website skeleton. 

Run the `serve` command to view your site!
```
./vendor/bin/jug serve
```
## What's next

Take a look at [the docs](docs/README.md) for more information.