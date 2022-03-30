<?php

namespace Jug\Twig;

use Jug\Config\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    public function __construct(
        private Config $config
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', [$this, 'addHash']),
        ];
    }

    public function addHash(string $path): string
    {
        if (!$this->config->has('hash')) {
            throw new \RuntimeException('Provide a valid hash in your config before using the asset function.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $baseName = basename($path, ".{$extension}");
        $hashedFile = $baseName . '.' . $this->config->get('hash') . '.' . $extension;

        return str_replace($baseName . '.' . $extension, $hashedFile, $path);
    }
}
