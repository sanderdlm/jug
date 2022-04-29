<?php

declare(strict_types=1);

namespace Jug\Command;

use Inotify\InotifyConsumerFactory;
use Inotify\InotifyEventCodeEnum;
use Inotify\WatchedResourceCollection;
use Jug\EventSubscriber\InotifyModifiedSubscriber;
use Jug\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'serve',
    description: 'Start a local webserver and watch the files in your source folder.',
)]
class ServeCommand extends Command
{
    public function __construct(
        private Generator $generator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'address',
                InputArgument::OPTIONAL,
                'Address:port',
                'localhost:8080'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $server = $this->startWebServer($input, $output);

        $this->watchFiles($output);

        return Command::SUCCESS;
    }

    private function startWebServer(InputInterface $input, OutputInterface $output): Process
    {
        /** @var string */
        $address = $input->getArgument('address');

        $webServer = new Process([PHP_BINARY, '-S', $address, '-t', $this->generator->getConfig()->get('output')]);
        $webServer->setTimeout(null);
        $webServer->start();

        $output->writeln(sprintf('Server running on <comment>http://%s</comment>', $address));

        return $webServer;
    }

    private function watchFiles(OutputInterface $output): void
    {
        $finder = new Finder();
        $sourceFolder = $this->generator->getConfig()->getString('source');
        $paths = [$sourceFolder, 'config.php'];

        if (is_file('events.php')) {
            $paths[] = 'events.php';
        }

        foreach ($finder->in($sourceFolder)->directories() as $directory) {
            $paths[] = $directory->getPathname();
        }

        (new InotifyConsumerFactory())
            ->registerSubscriber(new InotifyModifiedSubscriber($this->generator, $output))
            ->consume(WatchedResourceCollection::fromArray(
                $paths,
                InotifyEventCodeEnum::ON_CLOSE_WRITE,
                'fileChanged'
            ));
    }
}
