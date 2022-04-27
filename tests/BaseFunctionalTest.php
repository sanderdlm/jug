<?php

namespace Jug\Test;

use Jug\Config\Config;
use Jug\Generator;
use Jug\Twig\AssetExtension;
use Jug\Twig\DynamicFilesystemLoader;
use Jug\Twig\FolderExtension;
use Jug\Twig\HighlightExtension;
use Jug\Twig\MarkdownExtension;
use Jug\Twig\SqliteExtension;
use Michelf\Markdown;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

abstract class BaseFunctionalTest extends TestCase
{
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

        foreach ($config->getAll() as $key => $value) {
            $twig->addGlobal($key, $value);
        }

        // Translations
        if (!$config->has('default_locale')) {
            throw new RuntimeException('Missing required config option: default_locale.');
        }

        $translator = new Translator($config->getString('default_locale'));
        $translator->addLoader('yaml', new YamlFileLoader());

        if ($config->has('locales')) {
            foreach ($config->getArray('locales') as $locale) {
                $translationPath = 'translations/messages.' . $locale . '.yaml';

                if (is_file($translationPath)) {
                    $translator->addResource('yaml', $translationPath, $locale);
                }
            }
        } else {
            $translationPath = 'translations/messages.' . $config->get('default_locale') . '.yaml';

            if (is_file($translationPath)) {
                $translator->addResource('yaml', $translationPath, $config->getString('default_locale'));
            }
        }

        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new AssetExtension($config));
        $twig->addExtension(new MarkdownExtension(new Markdown()));
        $twig->addExtension(new HighlightExtension());
        $twig->addExtension(new SqliteExtension());
        $twig->addExtension(new FolderExtension());

        // Create the site generator object
        $this->generator = new Generator($twig, $filesystem, $config);
    }
}
