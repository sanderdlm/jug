<?php

declare(strict_types=1);

namespace Jug\Domain;

class File
{
    public function __construct(
        public readonly string $relativePath
    ) {
    }
}
