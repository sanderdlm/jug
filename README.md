# Jug

Jug is a very basic Symfony console application that turns Twig templates into straight HTML.

## Features 

### Twig powered

The templates are rendered using Twig, which gives you access to:
* includes
* blocks
* macros
* lots of useful filters
* See [the Twig docs](https://twig.symfony.com/doc/3.x/) for a full list

### Multi-lang ready

Jug includes the [symfony/translations](https://symfony.com/doc/current/translation.html#using-twig-filters) package, which means you can translate your templates using the `trans` filter in your Twig templates.

```
{{ 'goodmorning'|trans }}
```

If multiple locales are configured, Jug will generate a version of your site in each lanfuage.

### Cache friendly

You can use `{{ asset('logo.png) }}` to render assets and Jug will add a build-specific hash to all your assets (css, js, images) for easy cache-busting on deploy. This means you can aggressively cache those static assets in your webserver config for top-notch performance.

### Optimized images

Install optipng and jpegoptim on your local system and both will be used on all images when you build your site. A local cache of which images have already been optimised is kept to prevent multiple runs.

### Dead simple PHP configuration

Manage config, settings and other variables in config/jug.php. The content of the entire array will be passed to all your Twig templates.

```php
<?php

return [
    'default_locale' => 'en',
    'hash' => bin2hex(random_bytes(4)),
    'year' => (new DateTime('now'))->format('Y'),
    'image_cache' => 'images.json',
];
```

### Markdown

Use the markdown Twig filter to parse content as markdown:
`'some **markdown** content'|markdown`

Or use the Twig apply block to transform entire blocks:
```
{% apply markdown %}
 # More markdown!
 
 This could be an entire blog post!
 
 * a list
 * more list items
{% endapply %}
```

### Serve command for easy development

Running `./vendor/bin/jug serve` will launch the built-in PHP development server and watch all the files in the source folder. Your site will be regenerated every time you make a change.

## What it is not

Jug does not offer pagination or tag clouds. It does not use front-matter. It does not generate a full blog from a folder of markdown files.

It is a 1-to-1 Twig to HTML static site generator. If you want to end up with `/foo/bar/page.html`, you have to write `/foo/bar/page.twig`. You want 20 blog posts? Each post is a Twig file.

Think of it like building a HTML website by hand, with the added benefits of everything listed in the features above.

You can check out the tests/Fixture folder for a dummy example of a Jug website, or take a look at the [example repository](https://github.com/dreadnip/jug-template) to see it in use.