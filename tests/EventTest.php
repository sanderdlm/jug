<?php

namespace Jug\Test;

class EventTest extends BaseFunctionalTest
{
    public function testDynamicConfigValueFromBeforeEvent(): void
    {
        $fixtureTags = ['he', 'is', 'a', 'smooth', 'operator'];

        $this->assertIsArray($this->site->config->getarray('tags'));
        $this->assertEquals($fixtureTags, $this->site->config->getArray('tags'));
    }
}
