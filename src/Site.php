<?php

namespace Jug;

use Jug\Config\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class Site
{
    /**
     * @param array<string, SplFileInfo> $sourceFiles
     * @param array<string, SplFileInfo> $outputFiles
     */
    public function __construct(
        private readonly Config $config,
        private array $sourceFiles = [],
        private array $outputFiles = []
    ) {
        $this->collectSourceFiles();
    }

    private function collectSourceFiles(): void
    {
        $finder = (new Finder())
            ->in($this->config->getString('source'))
            ->exclude('_templates')
            ->files()
            ->name('*.twig');

        $this->sourceFiles = [...$finder->getIterator()];
    }

    public function collectOutputFiles(): void
    {
        $finder = (new Finder())
            ->in($this->config->getString('output'))
            ->exclude('assets')
            ->files()
            ->name('*.html');

        $this->outputFiles = [...$finder->getIterator()];
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return array<string, SplFileInfo>
     */
    public function getSourceFiles(): array
    {
        return $this->sourceFiles;
    }

    /**
     * @return array<string, SplFileInfo>
     */
    public function getOutputFiles(): array
    {
        return $this->outputFiles;
    }

    /**
     * @return array<string>
     */
    public function getSourceFolders(): array
    {
        $finder = new Finder();
        $paths = [];

        foreach ($finder->in($this->config->getString('source'))->directories() as $directory) {
            $paths[] = $directory->getPathname();
        }

        return $paths;
    }
}
