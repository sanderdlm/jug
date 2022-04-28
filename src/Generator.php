<?php

declare(strict_types=1);

namespace Jug;

use DOMDocument;
use DOMElement;
use Jug\Config\Config;
use Jug\Event\AfterBuild;
use Jug\Event\BeforeBuild;
use RuntimeException;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class Generator
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Filesystem $filesystem,
        private readonly Config $config,
        private readonly EventDispatcher $dispatcher
    ) {
    }

    public function generate(): void
    {
        $this->dispatcher->dispatch(new BeforeBuild($this), BeforeBuild::NAME);

        $sourceFolder = $this->config->getString('source');
        $outputFolder = $this->config->getString('output');

        $finder = new Finder();

        $this->filesystem->remove($outputFolder);
        $this->filesystem->mkdir($outputFolder);

        $finder->in($sourceFolder)->exclude('_templates')->files()->name('*.twig');

        if ($this->config->has('locales')) {
            foreach ($this->config->getArray('locales') as $locale) {
                assert(is_string($locale));
                $this->setLocale($locale);

                foreach ($finder as $file) {
                    $this->renderTemplate($file, $outputFolder, $locale);
                }
            }
        } else {
            foreach ($finder as $file) {
                $this->renderTemplate($file, $outputFolder);
            }
        }

        $this->filesystem->mirror($sourceFolder . '/assets', $outputFolder . '/assets');

        $this->compressImages($outputFolder . '/assets/images');

        if ($this->config->has('hash')) {
            $this->addHash($outputFolder . '/assets');
        }

        $this->dispatcher->dispatch(new AfterBuild($this), AfterBuild::NAME);
    }

    private function renderTemplate(SplFileInfo $file, string $outputFolder, ?string $locale = null): void
    {
        $renderedTemplate = $this->twig->render($file->getRelativePathname());

        $outputFormat = str_replace('twig', 'html', $file->getRelativePathname());

        if ($locale !== null) {
            $renderedTemplate = $this->makeInternalLinksLocaleAware($renderedTemplate, $locale);

            $outputPath = sprintf(
                '%s/%s/%s',
                $outputFolder,
                $locale,
                $outputFormat
            );
        } else {
            $outputPath = sprintf(
                '%s/%s',
                $outputFolder,
                $outputFormat
            );
        }

        $this->filesystem->dumpFile($outputPath, $renderedTemplate);
    }

    private function setLocale(string $locale): void
    {
        /** @var Translator $translator */
        $translator = $this->twig->getExtension(TranslationExtension::class)->getTranslator();
        $translator->setLocale($locale);
    }

    private function compressImages(string $imageFolder): void
    {
        if (!$this->config->has('image_cache')) {
            throw new RuntimeException('Missing required config option: image_cache.');
        }

        $imageCacheFile = $this->config->getString('image_cache');

        if (!is_file($imageCacheFile)) {
            $this->filesystem->touch($imageCacheFile);
        }

        $cacheContent = file_get_contents($this->config->getString('image_cache'));

        if (!$cacheContent) {
            $cacheContent = '';
        }

        /** @var array<string, array<string>> $cache */
        $cache = json_decode($cacheContent, true);

        $finder = new Finder();
        $finder->in($imageFolder)->files();

        if (!isset($cache['images']) || !is_array($cache['images'])) {
            $cache['images'] = [];
        }

        foreach ($finder as $image) {
            if (!in_array($image->getRealPath(), $cache['images'])) {
                if ($image->getExtension() === 'jpeg' || $image->getExtension() === 'jpg') {
                    shell_exec('jpegoptim -q -m85 -s --all-progressive ' . $image->getRealPath());
                } elseif ($image->getExtension() === 'png') {
                    shell_exec('optipng -o2 -i 0 -strip all -silent ' . $image->getRealPath());
                }
                $cache['images'][] = $image->getRealPath();
            }
        }

        file_put_contents($this->config->getString('image_cache'), json_encode($cache));
    }

    private function makeInternalLinksLocaleAware(string $html, string $locale): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);

        $nodes = $dom->getElementsByTagName('a');

        /** @var DOMElement $node */
        foreach ($nodes as $node) {
            $link = $node->getAttribute('href');
            if (str_contains($link, '.html')) {
                if (!str_starts_with($link, '/')) {
                    $link = '/' . $link;
                }

                $node->setAttribute('href', '/' . $locale . $link);
            }
        }

        $output = $dom->saveHTML();

        if ($output) {
            return $output;
        }

        return $html;
    }

    public function addHash(string $assetFolder): void
    {
        $finder = new Finder();

        $finder->in($assetFolder)->files();

        foreach ($finder as $file) {
            $extension = $file->getExtension();
            $baseName = $file->getFilenameWithoutExtension();
            $hashedName = $baseName . '.' . $this->config->get('hash') . '.' . $extension;

            $this->filesystem->rename(
                $file->getRealPath(),
                str_replace($baseName . '.' . $extension, $hashedName, $file->getRealPath())
            );
        }
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
