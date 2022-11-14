<?php

namespace Jug\Domain;

class Page
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public readonly File $source,
        public readonly File $output,
        public array $context = []
    ) {
    }

    public function addContext(array $context): void
    {
        $this->context = $context;
    }
}
