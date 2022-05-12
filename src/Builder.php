<?php

declare(strict_types=1);

namespace Jug;

use Jug\Config\Config;
use Jug\Domain\File;
use Jug\Domain\Page;
use Jug\Domain\Site;
use Jug\Twig\Parser;
use Symfony\Component\Finder\Finder;

final class Builder
{
    public function __construct(
        private readonly Config $config,
        private readonly Parser $parser
    ) {
    }

    public function build(): Site
    {
        return new Site($this->config, $this->collectPages());
    }

    /**
     * @return array<Page>
     */
    private function collectPages(): array
    {
        $pages = [];

        $finder = (new Finder())
            ->in($this->config->getString('source'))
            ->exclude('_templates')
            ->files()
            ->name('*.twig');

        foreach ($finder as $file) {
            $pages[] = new Page(
                new File($file->getRelativePathname()),
                new File($this->buildOutputPath($file->getRelativePathname())),
                $this->parser->parse($file->getRelativePathname())
            );
        }

        return $pages;
    }

    public function buildOutputPath(string $relativePath): string
    {
        return str_replace('twig', 'html', $relativePath);
    }
}
