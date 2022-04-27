<?php

namespace Jug\Test;

class GeneratorTest extends BaseFunctionalTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->generator->generate();
    }

    public function testSiteGeneration(): void
    {
        $this->assertDirectoryExists(__DIR__ . '/Fixture/output');
    }

    public function testTranslations(): void
    {
        $this->assertDirectoryExists(__DIR__ . '/Fixture/output/fr');
        $this->assertDirectoryExists(__DIR__ . '/Fixture/output/en');

        $this->assertFileExists(__DIR__ . '/Fixture/output/fr/translation.html');
        $this->assertFileExists(__DIR__ . '/Fixture/output/en/translation.html');

        $frenchFile = file_get_contents(__DIR__ . '/Fixture/output/fr/translation.html');
        $englishFile = file_get_contents(__DIR__ . '/Fixture/output/en/translation.html');

        if ($frenchFile) {
            $this->assertTrue(str_contains($frenchFile, 'Oui'));
        }

        if ($englishFile) {
            $this->assertTrue(str_contains($englishFile, 'Bar'));
        }
    }

    public function testAssetHashing(): void
    {
        $assetHashForThisBuild = $this->generator->getConfig()->getString('hash');

        $this->assertFileExists(__DIR__ . '/Fixture/output/assets/css/style.' . $assetHashForThisBuild . '.css');
        $this->assertFileExists(__DIR__ . '/Fixture/output/assets/images/logo.' . $assetHashForThisBuild . '.png');
    }
}
