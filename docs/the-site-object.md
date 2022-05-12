# The `Site` object

Before a Jug site is built, an index of your project is built and kept inside the Site object. This object is available in all your templates and inside the [events](events.md).

## Content

The object holds the following things:
* The config object as defined at the time of the build
* A list of all your pages. Each `Page` object has a souce property (the Twig template it built from) and an output property (the HTML file it rendered). If your site is multi-language, your output property will be an array of outputs. On top of the source and output of each template, you can also access the full context of that template.

Page example:
```
object(Jug\Domain\Page)#2613 (3) {
  ["source"]=>
  object(Jug\Domain\File)#2592 (1) {
    ["relativePath"]=>
    string(10) "index.twig"
  }
  ["output":"Jug\Domain\Page":private]=>
  array(2) {
    ["en"]=>
    object(Jug\Domain\File)#2532 (1) {
      ["relativePath"]=>
      string(13) "en/index.html"
    }
    ["fr"]=>
    object(Jug\Domain\File)#2471 (1) {
      ["relativePath"]=>
      string(13) "fr/index.html"
    }
  }
  ["context"]=>
  array(1) {
    ["title"]=>
    string(4) "Home"
  }
}
```

The context will hold any variables that you set inside that template. This allows you to define any type of metadata inside your template using a regular Twig `{% set %}` tag and access it from the site object.

## Using the site object inside your templates

### Config values
```twig
My source folder is: {{ site.config.get('source') }}
```
### Pages
```twig
{% for page in site.pages %}
    <li>{{ page.output.relativePath }}</li>
{% endfor %}
```
### Metadata
The site object has a select method that can select templates based on a key/value parameter inside the context.

For example, if you have multiple pages that you want to group together, you can define a common parameter inside those templates:
```twig
{% set type = 'travel-report' %}
```
And then collect all those templates
```twig
{% for page in site.select('type', 'travel-report') %}
    <li><a href="{{ page.getOutput.relativePath }}">{{ page.context['description'] }}</a></li>
{% endfor %}
```
The select method gives you the ability to group pages and render index or overview pages for them. You can use it to build blogs, or simply categorize pages together.