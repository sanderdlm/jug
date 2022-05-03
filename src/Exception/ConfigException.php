<?php

declare(strict_types=1);

namespace Jug\Exception;

use Exception;

class ConfigException extends Exception
{
    public static function missingFile(): self
    {
        return new self('Couldn\'t find a config.php file in your project.');
    }

    public static function missingKey(
        string $key,
    ): self {
        return new self(sprintf(
            'Missing required config key "%s".',
            $key
        ));
    }

    public static function missingPropertyTypeHint(
        string $propertyName,
        string $className,
    ): self {
        return new self(sprintf(
            'Missing type hint for property "%s" on class "%s".',
            $propertyName,
            $className
        ));
    }

    public static function missingMethod(
        string $methodName,
        string $className,
    ): self {
        return new self(sprintf(
            'Missing method "%s" on class "%s".',
            $methodName,
            $className
        ));
    }

    /**
     * @param array<string> $classes
     */
    public static function missingAttribute(
        string $attributeName,
        array $classes
    ): self {
        return new self(sprintf(
            'Missing attribute "%s" on classes: "%s"',
            $attributeName,
            implode(',', $classes)
        ));
    }

    public static function nonExistentClass(
        string $className,
    ): self {
        return new self(sprintf(
            'Class "%s" does not exist.',
            $className
        ));
    }

    public static function missingAbstractExtend(
        string $className,
    ): self {
        return new self(sprintf(
            'Class "%s" does not extend the AbstractDataTransferObject class.',
            $className
        ));
    }

    public static function mappedClassMismatch(
        string $mappedClassName,
        string $passedClassname,
    ): self {
        return new self(sprintf(
            'Passed object "%s" is not the same class as configured by the MapsTo attribute (%s)',
            $passedClassname,
            $mappedClassName
        ));
    }

    public static function updateWithoutSource(): self
    {
        return new self('Running an update without passing a source entity is invalid.');
    }
}
