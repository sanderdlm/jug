<?php

declare(strict_types=1);

namespace Jug\Command;

use Jug\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'build',
    description: 'Build your Jug site.',
)]
class BuildCommand extends Command
{
    public function __construct(
        private Kernel $kernel,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('build');

        $generator = $this->kernel->buildGenerator();

        $output->write('Building site..');

        $generator->generate();

        $buildTime = $stopwatch->stop('build');

        $output->writeln(' <info>Done! (in ' . $buildTime . 's)</info>');

        return Command::SUCCESS;
    }
}
