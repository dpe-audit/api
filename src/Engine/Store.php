<?php

namespace App\Engine;

final class Store
{
    /**
     * @var array<string, self>
     */
    private array $values = [];

    public function get(string $namespace, string $key, callable $callback): mixed
    {
        if (false === array_key_exists($namespace, $this->values)) {
            $this->values[$namespace] = new self();
        }
        if (false === array_key_exists($key, $this->values[$namespace]->values())) {
            $this->values[$namespace]->values()[$key] = $callback();
        }
        return $this->values[$namespace]->values()[$key];
    }

    public function has(string $namespace, ?string $key = null): bool
    {
        if (false === array_key_exists($namespace, $this->values)) {
            return false;
        }
        if (null === $key) {
            return true;
        }
        return array_key_exists($key, $this->values[$namespace]->values());
    }

    public function clear(): void
    {
        $this->values = [];
    }

    public function values(): array
    {
        return $this->values;
    }
}
