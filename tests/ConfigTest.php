<?php

namespace Jug\Test;

use DateTime;
use Jug\Config\Config;
use Jug\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $fixture;

    protected function setUp(): void
    {
        $dummyConfig = [
            'a string' => 'bar',
            'an array' => [
                'en',
                'fr'
            ],
            'an int' => 666,
            'a boolean' => true,
            'an object' => new DateTime('now'),
        ];
        $this->fixture = new Config($dummyConfig);
    }

    public function testHas(): void
    {
        $this->assertFalse($this->fixture->has('foo'));
        $this->assertTrue($this->fixture->has('an int'));
    }

    public function testGetThatDoesNotExist(): void
    {
        $this->expectException(ConfigException::class);

        $this->fixture->get('foo');
    }

    public function testValidGet(): void
    {
        $this->assertEquals(666, $this->fixture->get('an int'));
    }

    public function testGetString(): void
    {
        $this->assertIsString($this->fixture->getString('an int'));
    }

    public function testGetArray(): void
    {
        $this->assertIsArray($this->fixture->getArray('an int'));
    }
}
