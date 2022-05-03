<?php

declare(strict_types=1);

namespace Jug\Exception;

use Exception;

class ConfigException extends Exception
{
    public static function missingKey(
        string $key,
    ): self {
        return new self(sprintf(
            'Missing required config key "%s".',
            $key
        ));
    }

    public static function malformedContent(): self
    {
        return new self('Your config.php file should return a single array with string keys.');
    }
}
