<?php

namespace Jug\Twig;

use Twig\Loader\FilesystemLoader;

class DynamicFilesystemLoader extends FilesystemLoader
{
    /**
     * @param array<string> $paths
     */
    public function __construct(array $paths = [], string $rootPath = null)
    {
        parent::__construct($paths, $rootPath);
    }

    /*
     * By appending the current time to the cache key, we force
     * Twig to recompile templates during long PHP processes.
     */
    public function getCacheKey(string $name): string
    {
        return parent::getCacheKey($name) . time();
    }
}
