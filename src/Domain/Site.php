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
    public function select(string $key, ?string $value = null): array
    {
        return array_filter(
            $this->pages,
            function (Page $page) use ($key, $value) {
                if ($value === null) {
                    return array_key_exists($key, $page->context);
                } else {
                    return array_key_exists($key, $page->context) &&
                        $page->context[$key] === $value;
                }
            }
        );
    }

    /**
     * @return array<int, Page>
     */
    public function dir(string $folder): array
    {
        return array_filter(
            $this->pages,
            function (Page $page) use ($folder) {
                return str_contains($page->output->relativePath, $folder . DIRECTORY_SEPARATOR);
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

        foreach ($finder->in($this->config->getString('source')) as $sourceItem) {
            $paths[] = $sourceItem->getPathname();
        }

        return $paths;
    }

    /**
     * @return mixed[]
     */
    public function tree(): array
    {
        $tree = [];

        foreach ($this->pages as $page) {
            $parts = explode(DIRECTORY_SEPARATOR, $page->output->relativePath);
            $lastPart = &$tree;
            foreach ($parts as $part) {
                $lastPart = &$lastPart[$part];
            }

            $lastPart = $page;
        }

        return $tree;
    }
}
