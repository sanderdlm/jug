# Translations

Jug can generate a multi-lang version of your site thanks to the [`symfony/translations`](https://symfony.com/doc/current/translation.html) package. 

## Defining translations

All translation files go in the `translations` folder. Right now, only the  yaml format is supported. The exact format is the following:

`translations/messages.<locale>.yaml`

You can define translated strings in those files in regular yaml format:

```
Foo: Bar
'You can also use longer strings': 'And translate them like this'
or.you.can.use.keys: |
    And then translate them to longer
    
    multiline formatted text
    cool :)

```

## Usage

In the context of your Jug site you'll mostly work with the `trans` Twig filter:

```
{% block main %}
    <h1>{{ 'Foo'|trans }}</h1>
{% endblock %}
```
This will output the following structure:

![The translated folder structure](/docs/translations.png)

## Working with locales

The `default_locale` and other locales you define are available as a global variable in all your Twig templates and from the config object in the build events.
