<?php

declare(strict_types=1);

namespace Jug;

use Jug\Config\Config;
use Jug\Exception\ConfigException;
use Jug\Exception\FileSystemException;
use Jug\Twig\AssetExtension;
use Jug\Twig\DynamicFilesystemLoader;
use Jug\Twig\FolderExtension;
use Jug\Twig\HighlightExtension;
use Jug\Twig\MarkdownExtension;
use Jug\Twig\SqliteExtension;
use Michelf\Markdown;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;

class Kernel
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    public function buildGenerator(): Generator
    {
        if (!is_file('config.php')) {
            throw FileSystemException::missingFile('config.php');
        }

        $config = require 'config.php';

        if (!is_array($config)) {
            throw ConfigException::malformedContent();
        }

        $config = new Config($config);

        if (
            !$config->has('source') ||
            !$config->has('output')
        ) {
            ConfigException::missingKey('source & output');
        }

        $sourceFolder = $config->getString('source');
        $templateFolder = $sourceFolder . '/_templates';

        if (
            !$this->filesystem->exists($sourceFolder) ||
            !$this->filesystem->exists($templateFolder)
        ) {
            throw FileSystemException::missingDirectory('_templates');
        }

        $loader = new DynamicFilesystemLoader([$sourceFolder, $templateFolder]);
        $twig = new Environment($loader);

        foreach ($config->all() as $key => $value) {
            $twig->addGlobal($key, $value);
        }

        if (!$config->has('default_locale')) {
            throw ConfigException::missingKey('default_locale.');
        }

        $translator = new Translator($config->getString('default_locale'));
        $translator->addLoader('yaml', new YamlFileLoader());

        if ($config->has('locales')) {
            foreach ($config->getArray('locales') as $locale) {
                $translationPath = 'translations/messages.' . $locale . '.yaml';

                assert(is_string($locale));

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

        $dispatcher = new EventDispatcher();

        if (is_file('events.php')) {
            $eventFactory = require 'events.php';
            $eventFactory($dispatcher);
        }

        // Create the site data object
        $siteData = new Site($config);

        // Create the site generator object
        return new Generator($siteData, $twig, $this->filesystem, $dispatcher);
    }
}
