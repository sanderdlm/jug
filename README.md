# It's a jug!
Hacky prototype of very minimal static site generation with PHP. Intended for personal use only.

TL;DR:
Write Twig templates -> run `vendor/bin/jug build` -> get multilang HTML.

Will only work if your project adheres to a very specific directory structure. See [the example repository](https://github.com/dreadnip/jug-template) for the exact structure. I have no intention of adapting this into configuration options since I'm the only one using it.

## Features
### Twig
The templates are rendered using Twig, which gives you access to: 
  * includes
  * blocks
  * lots of useful filters
  * See [the Twig docs](https://twig.symfony.com/doc/3.x/) for a full list

All config options you set in `config/jug.php` are passed as global variables to all Twig templates.  

### Translations
symfony/translations is available. You can use {{ 'string'|trans }} in a template, provide the matching value in a yaml file in the `translations` folder and off you go.

### Optimized images
Both jpegoptim and optipng run on the `output/assets/images` folder.

### Cache busting hash
You can use `{{ asset('logo.png) }}` to render assets, and they will be suffixed by a hash that is unique to each build. This way you can aggressively cache all your static assets on prod. 