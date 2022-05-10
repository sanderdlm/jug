<?php

namespace Jug\Test;

use Jug\Config\Config;
use Jug\Domain\Page;

class BuilderTest extends BaseFunctionalTest
{
    public function testSiteObject(): void
    {
        $this->assertIsArray($this->site->pages);
        $this->assertInstanceOf(Config::class, $this->site->config);

        foreach ($this->site->pages as $page) {
            $this->assertInstanceOf(Page::class, $page);
        }
    }
}
