<?php

namespace Jug\Domain;

class Page
{
    /**
     * @param array<string, string> $context
     * @param File|array<File> $output
     */
    public function __construct(
        public readonly File $source,
        private readonly File|array $output,
        public readonly array $context = []
    ) {
    }

    /**
     * @return File|array<string, File>
     */
    public function getOutput(?string $locale = null): File|array
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
}
