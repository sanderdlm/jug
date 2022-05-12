<?php

declare(strict_types=1);

namespace Jug\Domain;

use Jug\Config\Config;
use Symfony\Component\Finder\Finder;

final class Site
{
    /**
     * @param array<int, Page> $pages
     */
    public function __construct(
        public readonly Config $config,
        public readonly array $pages
    ) {
    }

    /**
     * @return array<int, Page>
     */
    public function select(string $key, string $value): array
    {
        return array_filter(
            $this->pages,
            function (Page $page) use ($key, $value) {
                return \array_key_exists($key, $page->context) &&
                    $page->context[$key] === $value;
            }
        );
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