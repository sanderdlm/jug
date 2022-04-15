<?php

namespace Jug\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HighlightExtension extends AbstractExtension
{
    public function __construct()
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('highlight', [$this, 'highlight'], ['is_safe' => ['html']]),
        ];
    }

    public function highlight(string $string): string
    {
        ini_set("highlight.comment", "#20A672"); //green
        ini_set("highlight.default", "#212121"); //black
        ini_set("highlight.html", "#212121"); //light gray
        ini_set("highlight.keyword", "#236e9f; font-weight: bold"); //blue
        ini_set("highlight.string", "#C44B34"); //red

        $codeBlock = highlight_string($string, true);

        return str_replace('<code>', '<code class="block">', $codeBlock);
    }
}
