<?php

namespace Jug\Twig;

use Michelf\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    public function __construct(
        private Markdown $parser
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('markdown', [$this, 'load'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'parse'], ['is_safe' => ['html']]),
        ];
    }

    public function load(string $path, string $locale = null): ?string
    {
        $content = $this->getFile($path) ?? $this->getFile('source/' . $path) ?? null;

        if ($content !== null) {
            return $this->parse($content);
        }

        return null;
    }

    public function parse(string $content): string
    {
        // remove indentation
        if ($white = substr($content, 0, strspn($content, " \t\r\n\0\x0B"))) {
            $content = preg_replace("{^$white}m", '', $content);
        }

        return $this->parser->transform($content);
    }

    private function getFile(string $path): ?string
    {
        if (is_file($path)) {
            if ($content = file_get_contents($path)) {
                return $content;
            }
        }

        return null;
    }
}
