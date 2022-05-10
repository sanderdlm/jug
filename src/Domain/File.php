<?php

namespace Jug\Domain;

class File
{
    public function __construct(
        public readonly string $relativePath
    ) {
    }
}
