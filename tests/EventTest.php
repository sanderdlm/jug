<?php

namespace Jug\Test;

class EventTest extends BaseFunctionalTest
{
    public function testDynamicConfigValueFromEventEvent(): void
    {
        $this->assertTrue($this->site->config->get('a_dynamic_setting'));
    }
}
