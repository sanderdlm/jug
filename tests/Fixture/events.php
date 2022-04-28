<?php

use Jug\Event\AfterBuild;
use Jug\Event\BeforeBuild;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

/** @var $dispatcher EventDispatcher */

/** @var BeforeBuild $event */
$dispatcher->addListener(BeforeBuild::NAME, function (Event $event) {
});

/** @var AfterBuild $event */
$dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
});
