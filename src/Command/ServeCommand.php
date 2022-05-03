<?php

declare(strict_types=1);

namespace Jug\Command;

use Inotify\InotifyConsumerFactory;
use Inotify\InotifyEventCodeEnum;
use Inotify\WatchedResourceCollection;
use Jug\Config\Config;
use Jug\EventSubscriber\InotifyModifiedSubscriber;
use Jug\Generator;
use Jug\Kernel;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'serve',
    description: 'Start a local webserver and watch the files in your source folder.',
)]
class ServeCommand extends Command
{
    public function __construct(
        private readonly Kernel $kernel,
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
        $generator = $this->kernel->buildGenerator();

        $server = $this->startWebServer($generator->getSite()->getConfig(), $input, $output);

        $this->watchFiles($generator, $output);

        return Command::SUCCESS;
    }

    private function startWebServer(Config $config, InputInterface $input, OutputInterface $output): Process
    {
        /** @var string */
        $address = $input->getArgument('address');

        $webServer = new Process([
            PHP_BINARY,
            '-S', $address,
            '-t', $config->get('output')
        ]);
        $webServer->setTimeout(null);
        $webServer->start();

        $output->writeln(sprintf('Server running on <comment>http://%s</comment>', $address));

        return $webServer;
    }

    private function watchFiles(Generator $generator, OutputInterface $output): void
    {
        $sourceFolders = $generator->getSite()->getSourceFolders();
        $paths = ['config.php', ...$sourceFolders];

        if (is_file('events.php')) {
            $paths[] = 'events.php';
        }

        (new InotifyConsumerFactory())
            ->registerSubscriber(new InotifyModifiedSubscriber($generator, $output))
            ->consume(WatchedResourceCollection::fromArray(
                $paths,
                InotifyEventCodeEnum::ON_CLOSE_WRITE,
                'fileChanged'
            ));
    }
}
