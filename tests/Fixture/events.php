<?php

declare(strict_types=1);

use Jug\Event\AfterBuild;
use Jug\Event\BeforeBuild;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

return static function (EventDispatcher $dispatcher): void {
    $dispatcher->addListener(BeforeBuild::NAME, function (Event $event) {
        /** @var BeforeBuild $beforeBuild */
        $beforeBuild = $event;

        /*
         * An example of what you can build with the before event would be a tag cloud.
         */
        $tags = [];
        foreach ($beforeBuild->site->select('tags') as $page) {
            if (
                array_key_exists('tags', $page->context) &&
                is_array($page->context['tags'])
            ) {
                foreach ($page->context['tags'] as $tag) {
                    $tags[] = $tag;
                }
            }
        }

        $beforeBuild->site->config->add('tags', $tags);
    });

    $dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
        /** @var AfterBuild $afterBuild */
        $afterBuild = $event;

        /*
         * An example of what you can build with the after event would be a sitemap
         */

        $sitemap = new SomeSiteMapLibrary();
        foreach ($afterBuild->site->pages as $page) {
            $sitemap->addPage($page->output->relativePath);
        }
    });
};
