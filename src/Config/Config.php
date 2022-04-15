<?php

declare(strict_types=1);

namespace Jug\Config;

use RuntimeException;

class Config
{
    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private readonly array $values
    ) {
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function get(string $key): mixed
    {
        if (!array_key_exists($key, $this->values)) {
            throw new RuntimeException('Attempted to read config value for key: ' . $key . '. ');
        }

        return $this->values[$key];
    }

    public function getString(string $key): string
    {
        $value = $this->get($key);

        assert(is_string($value));

        return $value;
    }

    /**
     * @return array<string>
     */
    public function getArray(string $key): array
    {
        $value = $this->get($key);

        assert(is_array($value));

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        return $this->values;
    }
}
