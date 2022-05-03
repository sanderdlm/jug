<?php

declare(strict_types=1);

namespace Jug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'init',
    description: 'Set up a new, empty, skeleton of a Jug site.',
)]
class InitCommand extends Command
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (
            $this->filesystem->exists('config.php') ||
            $this->filesystem->exists('source/_templates')
        ) {
            $output->writeln(' <error>You can only initialize a new site in a blank folder.</error>');
        }

        $output->write('Setting up your new Jug site..');

        $defaultConfig = file_get_contents('vendor/dreadnip/jug/Fixture/default-config.txt');

        if ($defaultConfig) {
            $this->filesystem->appendToFile('config.php', $defaultConfig);
        }

        $this->filesystem->mkdir('source/_templates');

        $output->writeln(' <info>Done!</info>');

        return Command::SUCCESS;
    }
}
