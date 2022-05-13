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
        public readonly array $context = []
    ) {
    }
}
