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

        foreach ($beforeBuild->site->pages as $page) {
        }
    });

    $dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
        /** @var AfterBuild $afterBuild */
        $afterBuild = $event;

        foreach ($afterBuild->site->pages as $page) {
        }
    });
};
