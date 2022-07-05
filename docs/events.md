# Events

There are two events that are thrown during the Jug build process: BeforeBuild and AfterBuild. You can create an `events.php` file in your project root to listen for these events:

Example:
```php
<?php

declare(strict_types=1);

use Jug\Event\AfterBuild;
use Jug\Event\BeforeBuild;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

return static function (EventDispatcher $dispatcher): void {
    $dispatcher->addListener(BeforeBuild::NAME, function (Event $event) {
        /** @var BeforeBuild $beforeBuild */
        $beforeBuild = $event;
    });

    $dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
        /** @var AfterBuild $afterBuild */
        $afterBuild = $event;
        
        // Read config values
        $beforeBuild->site->config->get('whatever');

        // Loop over all pages of your site
        foreach ($afterBuild->site->pages as $page) {
        
        }
    });
};
```

The `$event` parameter has access to the `Site` object using `$event->site`. The site object is a container that holds lists of all the source files, all the output files and all configuration values. See [the site object](the-site-object.md) documentation.

## Examples

### Tag cloud (before event)
Event:
```php
$tags = [];

foreach ($beforeBuild->site->select('tags') as $page) {
    if (
        array_key_exists('tags', $page->context) &&
        is_array($page->context['tags'])
    ) {
        foreach ($page->context['tags'] as $tag) {
            $tags[] = $tag;
        }
    }
}

$beforeBuild->site->config->add('tags', $tags);
```
Usage:
```twig 
<ul>
{% for tag in site.config.get('tags') %}
    <li>{{ tag }}</li>
{% endfor %}
</ul>
```

### Sitemap (after event)

Event:
```php
$sitemap = new Generator('https://yoursite.com', '/some/path/sitemap.xml');

foreach ($afterBuild->site->pages as $page) {
    $sitemap->addURL($page->output->relativePath);
}

$sitemap->flush();
```
See [icamys/php-sitemap-generator](https://github.com/icamys/php-sitemap-generator)