<?php

namespace Jug\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContextExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('parseContext', [$this, 'addContext'], [
                'needs_context' => true,
            ]),
        ];
    }

    public function addContext(array $context): void
    {
        $contextCopy = $context;
        unset($contextCopy['site']);
        unset($contextCopy['page']);
        unset($contextCopy['currentLocale']);

        foreach ($context['site']->pages as $page) {
            if ($page === $context['page']) {
                $page->addContext($contextCopy);
            }
        }
    }
}
