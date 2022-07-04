<?php

namespace Jug\Crawler;

use Symfony\Component\DomCrawler\Crawler;

class HtmlCrawler
{
    public static function makeInternalLinksLocaleAware(string $html, string $locale): string
    {
        if ($html === '') {
            return '';
        }

        $crawler = new Crawler($html);

        foreach ($crawler->filterXPath('//a') as $linkNode) {
            if ($linkNode->attributes === null) {
                continue;
            }

            $linkHref = $linkNode->attributes->getNamedItem('href');

            if ($linkHref === null) {
                continue;
            }

            $linkTarget = $linkHref->nodeValue;

            if ($linkTarget !== null && str_contains($linkTarget, '.html')) {
                if (!str_starts_with($linkTarget, DIRECTORY_SEPARATOR)) {
                    $linkTarget = DIRECTORY_SEPARATOR . $linkTarget;
                }

                $linkTarget = DIRECTORY_SEPARATOR . $locale . $linkTarget;

                $linkHref->nodeValue = $linkTarget;
            }
        }

        return $crawler->outerHtml();
    }

    /**
     * @return array<string>
     */
    public static function getLinkTargets(string $html): array
    {
        $crawler = new Crawler($html);
        $targets = [];

        foreach ($crawler->filterXPath('//a') as $linkNode) {
            if ($linkNode->attributes === null) {
                continue;
            }

            $linkHref = $linkNode->attributes->getNamedItem('href');

            if ($linkHref === null) {
                continue;
            }

            $linkTarget = $linkHref->nodeValue;

            if ($linkTarget !== null && str_contains($linkTarget, '.html')) {
                $targets[] = $linkTarget;
            }
        }

        return $targets;
    }
}
