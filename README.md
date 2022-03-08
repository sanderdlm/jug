# It's a jug!
Hacky prototype of very minimal static site generation with PHP.

TL;DR:
Write Twig templates -> run `vendor/bin/jug build` -> get multilang HTML.

Will only work if your project adheres to a very specific directory structure.

## Features
### Twig
The templates are rendered using Twig, which gives you access to: 
  * includes
  * blocks
  * global variables
  * lots of useful filters
  * See [the Twig docs](https://twig.symfony.com/doc/3.x/) for a full list

### Translations
symfony/translations is available. You can use {{ 'string'|trans }} in a template, provide the matching value in a yaml file in the `translations` folder and off you go.

### Optimized images
Both jpegoptim and optipng run on the `assets/output/images` folder.
