<?php

declare(strict_types=1);

namespace Jug\Command;

use Jug\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'build',
    description: 'Build your Jug site.',
)]
class BuildCommand extends Command
{
    public function __construct(
        private Generator $generator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write('Building site..');

        $this->generator->generate();

        $output->writeln(' <info>Done!</info>');

        return Command::SUCCESS;
    }
}
