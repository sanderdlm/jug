<?php

namespace App\Command;

use DOMDocument;
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

class BuildSiteCommand extends Command
{
    private const SOURCE_FOLDER = 'source';
    private const OUTPUT_FOLDER = 'output';

    public function __construct(
        private Environment $twig,
        private Filesystem $filesystem,
        private array $config
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

        $finder->in(self::SOURCE_FOLDER)->depth(0)->files()->name('*.twig');

        $progressBar = new ProgressBar($output, count($finder));
        $progressBar->start();

        if ($this->isMultiLang()) {
            foreach ($this->config['locales'] as $locale) {
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

        $progressBar->finish();

        return Command::SUCCESS;
    }

    private function renderTemplate(SplFileInfo $file, ?string $locale = null)
    {
        $renderedTemplate = $this->twig->render($file->getFilename());

        $renderedTemplate = str_replace('assets/', '../assets/', $renderedTemplate);

        if ($locale !== null) {
            $renderedTemplate = $this->makeInternalLinksLocaleAware($renderedTemplate, $locale);

            $outputPath = sprintf(
                '%s/%s/%s.html',
                self::OUTPUT_FOLDER,
                $locale,
                $file->getFilenameWithoutExtension()
            );
        } else {
            $outputPath = sprintf(
                '%s/%s.html',
                self::OUTPUT_FOLDER,
                $file->getFilenameWithoutExtension()
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
        $finder = new Finder();
        $finder->in($imageFolder)->files();

        foreach ($finder as $image) {
            if ($image->getExtension() === 'jpeg' || $image->getExtension() === 'jpg') {
                shell_exec('jpegoptim -q -m85 -s --all-progressive ' . $image->getRealPath());
            } else if ($image->getExtension() === 'png') {
                shell_exec('optipng -o2 -i 0 -strip all -silent ' . $image->getRealPath());
            }
        }
    }

    private function makeInternalLinksLocaleAware(string $html, string $locale): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);

        $nodes = $dom->getElementsByTagName('a');

        foreach ($nodes as $node) {
            $link = $node->getAttribute('href');
            if (str_contains($link, '.html')) {
                $node->setAttribute('href', '/' . $locale . '/' . $link);
            }
        }

        return $dom->saveHTML();
    }

    private function isMultiLang(): bool
    {
        if (array_key_exists('locales', $this->config) &&
            count($this->config['locales']) > 1) {
            return true;
        }

        return false;
    }
}