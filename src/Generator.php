<?php

declare(strict_types=1);

namespace Jug;

use DOMDocument;
use DOMElement;
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

final class Generator
{
    public function __construct(
        private readonly Site $site,
        private readonly Environment $twig,
        private readonly Filesystem $filesystem,
        private readonly EventDispatcher $dispatcher,
    ) {
    }

    public function generate(): void
    {
        $this->dispatcher->dispatch(new BeforeBuild($this->site), BeforeBuild::NAME);

        $sourceFolder = $this->site->getConfig()->getString('source');
        $outputFolder = $this->site->getConfig()->getString('output');

        $this->filesystem->remove($outputFolder);
        $this->filesystem->mkdir($outputFolder);

        if ($this->site->getConfig()->has('locales')) {
            foreach ($this->site->getConfig()->getArray('locales') as $locale) {
                assert(is_string($locale));
                $this->setLocale($locale);

                foreach ($this->site->getSourceFiles() as $file) {
                    $this->renderTemplate($file, $outputFolder, $locale);
                }
            }
        } else {
            foreach ($this->site->getSourceFiles() as $file) {
                $this->renderTemplate($file, $outputFolder);
            }
        }

        $this->filesystem->mirror($sourceFolder . '/assets', $outputFolder . '/assets');

        $this->compressImages($outputFolder . '/assets/images');

        if ($this->site->getConfig()->has('hash')) {
            $this->addHash($outputFolder . '/assets');
        }

        $this->site->collectOutputFiles();

        $this->dispatcher->dispatch(new AfterBuild($this->site), AfterBuild::NAME);
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
        if (!$this->site->getConfig()->has('image_cache')) {
            throw new RuntimeException('Missing required config option: image_cache.');
        }

        $imageCacheFile = $this->site->getConfig()->getString('image_cache');

        if (!is_file($imageCacheFile)) {
            $this->filesystem->touch($imageCacheFile);
        }

        $cacheContent = file_get_contents($this->site->getConfig()->getString('image_cache'));

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

        file_put_contents($this->site->getConfig()->getString('image_cache'), json_encode($cache));
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

    private function addHash(string $assetFolder): void
    {
        $finder = new Finder();

        $finder->in($assetFolder)->files();

        foreach ($finder as $file) {
            $extension = $file->getExtension();
            $baseName = $file->getFilenameWithoutExtension();
            $hashedName = $baseName . '.' . $this->site->getConfig()->get('hash') . '.' . $extension;

            $this->filesystem->rename(
                $file->getRealPath(),
                str_replace($baseName . '.' . $extension, $hashedName, $file->getRealPath())
            );
        }
    }

    public function getSite(): Site
    {
        return $this->site;
    }
}
