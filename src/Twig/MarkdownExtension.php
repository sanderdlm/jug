<?php

namespace Jug\Twig;

use Michelf\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MarkdownExtension extends AbstractExtension
{
    public function __construct(
        private Markdown $parser
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('markdown', [$this, 'loadFile'], ['is_safe' => ['html']]),
        ];
    }

    public function loadFile(string $path, string $locale): ?string
    {
        $content = $this->getFile($path) ?? $this->getFile('source/' . $path) ?? null;

        if ($content !== null) {
            return $this->parser->transform($content);
        }
        
        return null;
    }

    private function getFile(string $path): ?string
    {
        if (is_file($path)) {
            return file_get_contents($path);
        }

        return null;
    }
}