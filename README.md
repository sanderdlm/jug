# Jug

Jug is a minimal static site generator built on Symfony components. It does exactly one thing: turn twig templates into straight HTML.

Jug aims to be fast & flexible. You won't find an extensive plugin system, but rather a set of basic yet powerful tools that you can use to build exactly what you need.

## Features

* Twig-powered
* Multilang ready (uses symfony/translations)
* Optimized images
* Watch & serve command for quick development
* Easy, PHP based config
* Events to customize the build before & after
* Smooth integration with Github Pages

## Getting started

```yaml
# Create a new project directory
mkdir my-project && cd my-project

# Install the package using composer
composer require dreadnip/jug

# Run the init command to generate your base site
./vendor/bin/jug init

# Run the serve command to view your site
./vendor/bin/jug serve
```
## What's next

Take a look at [the docs](docs/README.md) for more information.