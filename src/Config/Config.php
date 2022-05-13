<?php

declare(strict_types=1);

namespace Jug\Config;

use Jug\Exception\ConfigException;

class Config
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private array $values
    ) {
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->values)) {
            throw ConfigException::missingKey($key);
        }

        return $this->values[$key];
    }

    public function getString(string $key): string
    {
        $value = $this->get($key);

        return strval($value);
    }

    /**
     * @return array<mixed>
     */
    public function getArray(string $key): array
    {
        $value = $this->get($key);

        if (is_array($value)) {
            return $value;
        };

        return [$value];
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }

    public function add(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }
}
