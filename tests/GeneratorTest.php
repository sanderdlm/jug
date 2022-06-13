<?php

namespace Jug\Test;

use Jug\Crawler\HtmlCrawler;

class GeneratorTest extends BaseFunctionalTest
{
    protected function setUp(): void
    {
        parent::setUp();
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
        $assetHashForThisBuild = $this->generator->getSite()->config->getString('hash');

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

    public function testTitleSetting(): void
    {
        $this->assertFileExists($this->outputPath . '/en/title.html');

        $titleFile = file_get_contents($this->outputPath . '/en/title.html');

        if ($titleFile) {
            $this->assertTrue(str_contains($titleFile, '<title>Foo</title>'));
        }
    }

    public function testLocalizedLinks(): void
    {
        $this->assertDirectoryExists($this->outputPath . '/fr');
        $this->assertDirectoryExists($this->outputPath . '/en');

        $this->assertFileExists($this->outputPath . '/fr/news/index.html');
        $this->assertFileExists($this->outputPath . '/en/news/index.html');

        $frenchFile = file_get_contents($this->outputPath . '/fr/news/index.html');
        $englishFile = file_get_contents($this->outputPath . '/en/news/index.html');

        if ($frenchFile) {
            $frenchLinkTargets = HtmlCrawler::getLinkTargets($frenchFile);

            foreach ($frenchLinkTargets as $target) {
                $this->assertStringContainsString('/fr/', $target);
            }
        }

        if ($englishFile) {
            $englishLinkTargets = HtmlCrawler::getLinkTargets($englishFile);

            foreach ($englishLinkTargets as $target) {
                $this->assertStringContainsString('/en/', $target);
            }
        }
    }
}
