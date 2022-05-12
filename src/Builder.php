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
            $output = $this->buildLocaleAwareOutputPath($file->getRelativePathname());

            $pages[] = new Page(
                new File($file->getRelativePathname()),
                $output,
                $this->parser->parse($file->getRelativePathname())
            );
        }

        return $pages;
    }

    /**
     * @return File|array<File>
     */
    private function buildLocaleAwareOutputPath(string $relativePath): File|array
    {
        if ($this->config->has('locales')) {
            $output = [];
            foreach ($this->config->getArray('locales') as $locale) {
                assert(is_string($locale));

                $outputPath = $this->buildOutputPath($relativePath, $locale);
                $output[$locale] = new File($outputPath);
            }
        } else {
            $outputPath = $this->buildOutputPath($relativePath);
            $output = new File($outputPath);
        }

        return $output;
    }

    public function buildOutputPath(string $relativePath, ?string $locale = null): string
    {
        $outputFileName = str_replace('twig', 'html', $relativePath);

        if ($locale !== null) {
            $outputFileName = $locale . DIRECTORY_SEPARATOR . $outputFileName;
        }

        return $outputFileName;
    }
}
