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

        foreach ($afterBuild->getSite()->getOutputFiles() as $info) {
            //dump($info->getRelativePathname());
        }
    });
};
