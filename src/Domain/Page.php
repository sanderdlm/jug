<?php

namespace Jug\Domain;

class Page
{
    /**
     * @param array<string, string> $context
     * @param File|array<File> $output
     */
    public function __construct(
        private readonly File $source,
        private File|array $output,
        private readonly array $context = []
    ) {
    }

    public function getSource(): File
    {
        return $this->source;
    }

    /**
     * @return File|array<string, File>
     */
    public function getFile(?string $locale = null): File|array
    {
        if (
            is_array($this->output) &&
            $locale !== null &&
            array_key_exists($locale, $this->output)
        ) {
            return $this->output[$locale];
        }

        return $this->output;
    }

    /**
     * @return array<string, string>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
