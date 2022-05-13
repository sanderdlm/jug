# Translations

Jug can generate a multi-lang version of your site thanks to the [`symfony/translations`](https://symfony.com/doc/current/translation.html) package. 

See [the Symfony docs](https://symfony.com/doc/current/translation.html) for extensive documentation.

## Configuration
The first step to activating multi-language building of your site is defining the locales you want in your config.php.

Both the `default_locale` string and the `locales` array are required. 

Example of a multi-language configuration:
```php
<?php

return [
    'source' => 'source',
    'output' => 'output',
    'default_locale' => 'en',
    'locales' => [
        'en',
        'fr'
    ],
    'hash' => bin2hex(random_bytes(4)),
    'image_cache' => 'images.json',
];
```
## Defining translations

All translation files go in the `translations` folder. Right now, only the  yaml format is supported. The exact format is the following:

`translations/messages.<locale>.yaml`

Make sure you have a translations file for each string you entered in your `locales` configuration array.

You can define translated strings in those files in regular yaml format:
```
hello: bonjour
'You can also use longer strings': 'And translate them like this'
or.you.can.use.keys: |
    And then translate them to longer
    
    multiline formatted text
    cool :)

```

## Usage

In the context of your Jug site you'll mostly work with the `trans` Twig filter

If you have a file called `translations.twig`:
```
{% block main %}
    <h1>{{ 'Foo'|trans }}</h1>
{% endblock %}
```
This will output the following structure:

![The translated folder structure](/docs/translations.png)

Your user can now visit either the `/en/` version or the `/fr/` version of your site.

## Switching languages

It's trivial to include a simple language switcher using the configuration from the site object.

```twig
{% if site.config.get('locales')|length > 1 %}
    <div class="language-dropdown">
      {% for locale in site.config.get('locales') %}
      <a href="/{{ locale }}/">{{ locale|trans }}</a>{% if not loop.last %}/{% endif %}
      {% endfor %}
    </div>
{% endif %}
```