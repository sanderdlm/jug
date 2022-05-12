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
    });
};
```

The `$event` parameter has access to the `Site` object using `$event->site`. The site object is a container that holds lists of all the source files, all the output files and all configuration values. See [the site object](the-site-object.md) documentation.
