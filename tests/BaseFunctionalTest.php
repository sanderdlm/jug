<?php

namespace Jug\Test;

use Jug\Builder;
use Jug\Config\Config;
use Jug\Domain\Site;
use Jug\Generator;
use Jug\Twig\AssetExtension;
use Jug\Twig\DynamicFilesystemLoader;
use Jug\Twig\HighlightExtension;
use Jug\Twig\MarkdownExtension;
use Jug\Twig\Parser;
use Jug\Twig\SqliteExtension;
use Michelf\MarkdownExtra;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Extension\DebugExtension;

abstract class BaseFunctionalTest extends TestCase
{
    protected Site $site;
    protected Builder $builder;
    protected Generator $generator;
    protected string $fixturePath;
    protected string $outputPath;

    protected function setUp(): void
    {
        $this->fixturePath = __DIR__ . '/Fixture';
        $this->outputPath = __DIR__ . '/Fixture/output';

        $filesystem = new Filesystem();

        $config = require 'Fixture/config.php';
        $config = new Config($config);

        $sourceFolder = $config->get('source');
        $templateFolder = $sourceFolder . '/_templates';

        // Twig
        $loader = new DynamicFilesystemLoader([$sourceFolder, $templateFolder]);
        $twig = new Environment($loader);

        $translator = new Translator($config->getString('default_locale'));
        $translator->addLoader('yaml', new YamlFileLoader());

        if ($config->has('locales')) {
            foreach ($config->getArray('locales') as $locale) {
                $translationPath = __DIR__ . '/Fixture/translations/messages.' . $locale . '.yaml';

                if (is_file($translationPath)) {
                    $translator->addResource('yaml', $translationPath, $locale);
                }
            }
        } else {
            $translationPath = __DIR__ . '/Fixture/translations/messages.' . $config->get('default_locale') . '.yaml';

            if (is_file($translationPath)) {
                $translator->addResource('yaml', $translationPath, $config->getString('default_locale'));
            }
        }

        $dispatcher = new EventDispatcher();

        $eventFactory = require 'Fixture/events.php';
        $eventFactory($dispatcher);

        // Create the site data object
        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new AssetExtension($config));
        $twig->addExtension(new MarkdownExtension(new MarkdownExtra()));
        $twig->addExtension(new HighlightExtension());
        $twig->addExtension(new SqliteExtension());

        $parser = new Parser($twig);

        // Create the site data object
        $this->builder = new Builder($config, $parser);

        // Create the site generator object
        $this->generator = new Generator($this->builder, $twig, $filesystem, $dispatcher);

        $this->generator->generate();

        $this->site = $this->generator->getSite();
    }
}
