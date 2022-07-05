<?php

namespace Jug\Test;

class ComponentTest extends BaseFunctionalTest
{
    public function testMenu(): void
    {
        $tree = $this->site->tree();

        $this->assertIsArray($tree);
        $this->assertArrayHasKey('news', $tree);
        $this->assertArrayHasKey('demo', $tree);
        $this->assertArrayHasKey('nested', $tree['demo']);
    }
}
