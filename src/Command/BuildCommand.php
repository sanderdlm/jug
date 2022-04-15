<?php

declare(strict_types=1);

namespace Jug\Command;

use DOMDocument;
use DOMElement;
use Jug\Config\Config;
use RuntimeException;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class BuildCommand extends Command
{
    private const SOURCE_FOLDER = 'source';
    private const OUTPUT_FOLDER = 'output';
    private const EXCLUDES = [
        '_includes',
        '_layouts' ,
    ];

    public function __construct(
        private Environment $twig,
        private Filesystem $filesystem,
        private Config $config
    ) {
        parent::__construct();
    }
    protected static $defaultName = 'build';

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();

        $this->filesystem->remove(self::OUTPUT_FOLDER);
        $this->filesystem->mkdir(self::OUTPUT_FOLDER);

        $finder->in(self::SOURCE_FOLDER)->exclude(self::EXCLUDES)->files()->name('*.twig');

        $output->writeln('<info>Building site...</info>');
        $progressBar = new ProgressBar($output, count($finder));
        $progressBar->start();

        if (null !== $locales = $this->getLocales()) {
            foreach ($locales as $locale) {
                assert(is_string($locale));
                $this->setLocale($locale);

                foreach ($finder as $file) {
                    $this->renderTemplate($file, $locale);

                    $progressBar->advance();
                }
            }
        } else {
            foreach ($finder as $file) {
                $this->renderTemplate($file);

                $progressBar->advance();
            }
        }

        // TODO: CSS? Do we need Sass/JS transpile?
        $this->filesystem->mirror(self::SOURCE_FOLDER . '/assets', self::OUTPUT_FOLDER . '/assets');

        $this->compressImages(self::OUTPUT_FOLDER . '/assets/images');

        if ($this->config->has('hash')) {
            $this->addHash(self::OUTPUT_FOLDER . '/assets');
        }

        $progressBar->finish();

        $output->writeln(' <info>Building done!</info>');

        return Command::SUCCESS;
    }

    private function renderTemplate(SplFileInfo $file, ?string $locale = null): void
    {
        $renderedTemplate = $this->twig->render($file->getRelativePathname());

        $renderedTemplate = str_replace('assets/', '../assets/', $renderedTemplate);

        $outputFormat = str_replace('twig', 'html', $file->getRelativePathname());

        if ($locale !== null) {
            $renderedTemplate = $this->makeInternalLinksLocaleAware($renderedTemplate, $locale);

            $outputPath = sprintf(
                '%s/%s/%s',
                self::OUTPUT_FOLDER,
                $locale,
                $outputFormat
            );
        } else {
            $outputPath = sprintf(
                '%s/%s',
                self::OUTPUT_FOLDER,
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

        $cacheContent = file_get_contents($this->config->getString('image_cache'));

        if (!$cacheContent) {
            throw new RuntimeException('Could\'nt get image cache content.');
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
                $node->setAttribute('href', '/' . $locale . '/' . $link);
            }
        }

        $output = $dom->saveHTML();

        if ($output) {
            return $output;
        }

        return $html;
    }

    /**
     * @return array<string>|null
     */
    private function getLocales(): ?array
    {
        if (
            $this->config->has('locales')
        ) {
            return $this->config->getArray('locales');
        }

        return null;
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
}
