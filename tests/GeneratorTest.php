<?php

namespace Jug\Test;

class GeneratorTest extends BaseFunctionalTest
{
    private string $fixturePath;
    private string $outputPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixturePath = __DIR__ . '/Fixture';
        $this->outputPath = __DIR__ . '/Fixture/output';

        $this->generator->generate();
    }

    public function testSiteGeneration(): void
    {
        $this->assertDirectoryExists($this->outputPath);
    }

    public function testTranslations(): void
    {
        $this->assertDirectoryExists($this->outputPath . '/fr');
        $this->assertDirectoryExists($this->outputPath . '/en');

        $this->assertFileExists($this->outputPath . '/fr/translation.html');
        $this->assertFileExists($this->outputPath . '/en/translation.html');

        $frenchFile = file_get_contents($this->outputPath . '/fr/translation.html');
        $englishFile = file_get_contents($this->outputPath . '/en/translation.html');

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

        $this->assertFileExists($this->outputPath . '/assets/css/style.' . $assetHashForThisBuild . '.css');
        $this->assertFileExists($this->outputPath . '/assets/images/logo.' . $assetHashForThisBuild . '.png');
    }

    public function testImageCache(): void
    {
        $this->assertFileExists($this->fixturePath . '/images.json');
    }

    public function testMarkdownFilter(): void
    {
        $this->assertFileExists($this->fixturePath . '/images.json');
    }
}
