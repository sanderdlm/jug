<?php

declare(strict_types=1);

namespace Jug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'init',
    description: 'Set up a new, empty, skeleton of a Jug site.',
)]
class InitCommand extends Command
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (
            $this->filesystem->exists('config.php') ||
            $this->filesystem->exists('source/_templates')
        ) {
            $output->writeln(' <error>You can only initialize a new site in a blank folder.</error>');
        }

        $output->write('Setting up your new Jug site..');

        $this->filesystem->appendToFile('config.php', $this->getDefaultConfig());

        $this->filesystem->mkdir('source/_templates');
        $this->filesystem->mkdir('source/assets/images');
        $this->filesystem->mkdir('source/assets/css');
        $this->filesystem->mkdir('source/assets/js');

        $this->filesystem->appendToFile('source/assets/css/style.css', $this->getDefaultStylesheet());
        $this->filesystem->appendToFile('source/_templates/base.twig', $this->getDefaultBaseTemplate());
        $this->filesystem->appendToFile('source/index.twig', $this->getDefaultIndexTemplate());

        $output->writeln(' <info>Done!</info>');

        return Command::SUCCESS;
    }

    private function getDefaultConfig(): string
    {
        return <<<DOC
        <?php
        
        return [
            'source' => 'source',
            'output' => 'output',
            'default_locale' => 'en',
            'hash' => bin2hex(random_bytes(4)),
            'image_cache' => 'images.json',
        ];
        DOC;
    }

    private function getDefaultBaseTemplate(): string
    {
        return <<<DOC
        {{ parseContext() }}
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>{{ title ?? '🍃 Your new site' }}</title>
            <meta name="description" content="Your new site">
            <meta name="author" content="You">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" type="text/css" href="{{ asset('/assets/css/style.css') }}">
        </head>
        <body>
            {% block content %}{% endblock %}
        </body>
        </html>
        DOC;
    }


    private function getDefaultIndexTemplate(): string
    {
        return <<<DOC
        {% extends 'base.twig' %}
        
        {% set title = '👋 Welcome to Jug!' %}
        
        {% block content %}
            <h1>Welcome 👋</h1>
            <p>Thanks for checking out Jug!</p>
            <p>Start your site by modifying this template.</p>
            <p>
                If you have any questions, please check out 
                <a href="https://github.com/dreadnip/jug/tree/master/docs">the documentation</a>.
            </p>
        {% endblock %}
        DOC;
    }

    private function getDefaultStylesheet(): string
    {
        return <<<DOC
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system,BlinkMacSystemFont,Helvetica Neue,Arial,Noto Sans,
            sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;
            font-size: 1.3rem;
            color: #333;
            overflow-y: scroll;
            line-height: 1.5;
            padding: 3rem;
            background-color: #fafafa;
        }
        
        body * + * {
            margin-top: 2rem;
        }
        DOC;
    }
}
