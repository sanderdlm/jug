<?php

namespace Jug\Twig;

use Jug\Domain\Site;
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

    /**
     * @param array<string, mixed> $context
     */
    public function addContext(array $context): void
    {
        $contextCopy = $context;
        unset($contextCopy['site']);
        unset($contextCopy['page']);
        unset($contextCopy['currentLocale']);

        /** @var Site $siteObject */
        $siteObject = $context['site'];

        foreach ($siteObject->pages as $page) {
            if ($page === $context['page']) {
                $page->addContext($contextCopy);
            }
        }
    }
}
