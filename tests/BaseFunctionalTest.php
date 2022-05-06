<?php

namespace Jug\Test;

use Jug\Config\Config;
use Jug\Generator;
use Jug\Site;
use Jug\Twig\AssetExtension;
use Jug\Twig\DynamicFilesystemLoader;
use Jug\Twig\FolderExtension;
use Jug\Twig\HighlightExtension;
use Jug\Twig\MarkdownExtension;
use Jug\Twig\SqliteExtension;
use Michelf\Markdown;
use Michelf\MarkdownExtra;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

abstract class BaseFunctionalTest extends TestCase
{
    protected Site $site;
    protected Generator $generator;

    protected function setUp(): void
    {
        $filesystem = new Filesystem();

        $config = require 'Fixture/config.php';
        $config = new Config($config);

        $sourceFolder = $config->getString('source');
        $templateFolder = $sourceFolder . '/_templates';

        // Twig
        $loader = new DynamicFilesystemLoader([$sourceFolder, $templateFolder]);
        $twig = new Environment($loader, [
            'auto_reload' => true
        ]);

        foreach ($config->all() as $key => $value) {
            $twig->addGlobal($key, $value);
        }

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

        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new AssetExtension($config));
        $twig->addExtension(new MarkdownExtension(new MarkdownExtra()));
        $twig->addExtension(new HighlightExtension());
        $twig->addExtension(new SqliteExtension());
        $twig->addExtension(new FolderExtension());

        $dispatcher = new EventDispatcher();

        $eventFactory = require 'Fixture/events.php';
        $eventFactory($dispatcher);

        // Create the site data object
        $this->site = new Site($config);

        // Create the site generator object
        $this->generator = new Generator($this->site, $twig, $filesystem, $dispatcher);
    }
}
