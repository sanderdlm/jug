<?php

declare(strict_types=1);

namespace Jug\EventSubscriber;

use Inotify\InotifyEvent;
use Jug\Generator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InotifyModifiedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Generator $generator,
        private OutputInterface $output
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [InotifyEvent::class => 'onInotifyEvent'];
    }

    public function onInotifyEvent(InotifyEvent $event): void
    {
        $this->generator->generate();

        $this->output->writeln('<info>Site rebuilt</info>');
    }
}
