<?php

declare(strict_types=1);

namespace Jug\Command;

use Inotify\WatchedResource;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Inotify\InotifyConsumerFactory;
use Inotify\InotifyEventCodeEnum;
use Inotify\WatchedResourceCollection;
use Jug\EventSubscriber\InotifyModifiedSubscriber;
use Symfony\Component\Finder\Finder;

class WatchCommand extends Command
{
    private const SOURCE_FOLDER = 'source';

    public function __construct(
    ) {
        parent::__construct();
    }
    protected static $defaultName = 'watch';

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Watching the files in '  . self::SOURCE_FOLDER. '...</info>');
        $command = $this->getApplication()->find('build');
        $flags = InotifyEventCodeEnum::ON_CLOSE_WRITE()->getValue();

        $finder = new Finder();
        $finder->in(self::SOURCE_FOLDER)->directories();

        $collection = WatchedResourceCollection::createSingle(
            self::SOURCE_FOLDER,
            $flags,
            'fileChanged'
        );
        
        foreach ($finder as $directory) {
            $collection->push(new WatchedResource($directory->getPathname(), $flags, 'fileChanged'));
        }

        (new InotifyConsumerFactory())
            ->registerSubscriber(new InotifyModifiedSubscriber($command, $output))
            ->consume($collection);

        return Command::SUCCESS;
    }
}