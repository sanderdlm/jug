<?php

namespace Jug\Test;

use Jug\Config\Config;
use Jug\Domain\Page;

class SiteTest extends BaseFunctionalTest
{
    public function testSiteObject(): void
    {
        $this->assertIsArray($this->site->pages);
        $this->assertInstanceOf(Config::class, $this->site->config);

        foreach ($this->site->pages as $page) {
            $this->assertInstanceOf(Page::class, $page);
        }
    }

    public function testSiteSelect(): void
    {
        $articles = $this->site->select('type', 'article');
        $this->assertIsArray($articles);
        $this->assertCount(2, $articles);

        foreach ($articles as $article) {
            $this->assertArrayHasKey('type', $article->context);
            $this->assertEquals('article', $article->context['type']);
        }
    }

    public function testTags(): void
    {
        $pagesWithTags = $this->site->select('tags');

        foreach ($pagesWithTags as $page) {
            $this->assertIsArray($page->context['tags']);
        }
    }

    public function testSiteDir(): void
    {
        $newsFolderContent = $this->site->dir('news');
        $this->assertIsArray($newsFolderContent);
        $this->assertCount(3, $newsFolderContent);

        foreach ($newsFolderContent as $item) {
            $this->assertStringContainsString('news' . DIRECTORY_SEPARATOR, $item->output->relativePath);
        }
    }
}
