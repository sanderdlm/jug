# It's a jug!
Just writing HTML with a little extra spice

Running `vendor/bin/jug build` will transport the HTML you write in the `/source` folder to `/output`. In the process a little extra spice will be aplied.

## Twig
The templates will be parsed by Twig, which means you get access to anything Twig offers: 
  * includes
  * global variables
  * See [the Twig docs](https://twig.symfony.com/doc/3.x/) for a full list

## Translations
symfony/translations is available. You can use {{ 'string'|trans }} in a template, provide the matching value in the `translations` folder and off you go.

## Optimized images
Both jpegoptim and optipng are ran on the `assets/output/images` folder.
