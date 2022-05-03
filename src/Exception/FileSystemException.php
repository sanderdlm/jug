<?php

declare(strict_types=1);

namespace Jug\Exception;

use Exception;

class FileSystemException extends Exception
{
    public static function missingFile(
        string $fileName
    ): self {
        return new self(sprintf(
            'Missing file: "%s".',
            $fileName
        ));
    }

    public static function missingDirectory(
        string $directoryName
    ): self {
        return new self(sprintf(
            'Missing directory: %s',
            $directoryName,
        ));
    }
}
