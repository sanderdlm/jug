<?php

namespace Jug\Test;

class GeneratorTest extends BaseFunctionalTest
{
    public function testSiteGeneration(): void
    {
        $this->generator->generate();

        $this->assertDirectoryExists(__DIR__ . '/Fixture/output');
    }
}
