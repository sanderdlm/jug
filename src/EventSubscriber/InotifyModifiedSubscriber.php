<?php

declare(strict_types=1);

namespace Jug\EventSubscriber;

use Inotify\InotifyEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class InotifyModifiedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Command $command,
        private OutputInterface $output,
        private array $cache = [],
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [InotifyEvent::class => 'onInotifyEvent'];
    }

    public function onInotifyEvent(InotifyEvent $event): void
    {
        $buffer = 2;
        $fileName = $event->getFileName();

        // Work-around for temp files with a tilde appendix
        if (substr($fileName, -1) === '~') {
            $fileName = substr($fileName, 0, -1);
        }

        // Prevent duplicate events
        if (array_key_exists($fileName, $this->cache) &&
            $this->cache[$fileName] >= time() - $buffer &&
            $this->cache[$fileName] <= time() + $buffer) {
            return;
        }

        $this->cache[$fileName] = time();
        $this->command->run(new ArrayInput([]), $this->output);
    }
}