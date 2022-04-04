<?php

declare(strict_types=1);

namespace Jug\EventSubscriber;

use Inotify\InotifyEvent;
use Jug\Command\BuildCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InotifyModifiedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ?BuildCommand $command,
        private OutputInterface $output
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [InotifyEvent::class => 'onInotifyEvent'];
    }

    public function onInotifyEvent(InotifyEvent $event): void
    {
        $this->command?->run(new ArrayInput([]), $this->output);
    }
}
