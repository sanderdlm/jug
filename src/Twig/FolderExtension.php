<?php

namespace Jug\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Finder\Finder;

class FolderExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dir', [$this, 'getContents']),
        ];
    }

    /**
     * @return array<string>
     */
    public function getContents(string $path): array
    {
        $files = [];
        $finder = new Finder();

        $finder->in($path)->files()->name('*.twig');

        foreach ($finder as $file) {
            $files[$file->getFilenameWithoutExtension()] = $file->getFilenameWithoutExtension() . '.html';
        }

        return $files;
    }
}
