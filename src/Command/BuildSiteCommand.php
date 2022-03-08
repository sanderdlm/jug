<?php

namespace App\Command;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class BuildSiteCommand extends Command
{
    private const SOURCE_FOLDER = 'source';
    private const OUTPUT_FOLDER = 'output';
    private const LOCALES = [
        'nl',
        'fr'
    ];

    public function __construct(
        private Environment $twig
    ) {
        parent::__construct();
    }
    protected static $defaultName = 'build';

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileSystem = new Filesystem();
        $finder = new Finder();

        $finder->in(self::SOURCE_FOLDER)->depth(0)->files()->name('*.twig');

        $progressBar = new ProgressBar($output, count($finder));
        $progressBar->start();

        foreach (self::LOCALES as $locale) {
            $this->setLocale($locale);

            foreach ($finder as $file) {
                $renderedTemplate = $this->twig->render($file->getFilename());

                $renderedTemplate = str_replace('assets/', '../assets/', $renderedTemplate);

                $fileSystem->dumpFile(
                    self::OUTPUT_FOLDER . '/' . $locale . '/' . $file->getFilenameWithoutExtension() . '.html',
                    $renderedTemplate
                );

                $progressBar->advance();
            }
        }

        // TODO: CSS? Do we need Sass/JS transpile?
        $fileSystem->mirror(self::SOURCE_FOLDER . '/assets', self::OUTPUT_FOLDER . '/assets');

        $this->compressImages(self::OUTPUT_FOLDER . '/assets/images');

        $progressBar->finish();

        return Command::SUCCESS;
    }

    private function setLocale(string $locale): void
    {
        assert($this->twig->getExtension(TranslationExtension::class)->getTranslator() instanceof Translator);
        $this->twig->getExtension(TranslationExtension::class)->getTranslator()->setLocale($locale);
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
}