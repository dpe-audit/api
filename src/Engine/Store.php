<?php

namespace App\Engine;

final class Store
{
    /**
     * @var array<string, mixed>
     */
    private array $values = [];

    public function get(string $namespace, string $key, callable $callback): mixed
    {
        $key = "$namespace::$key";
        if (false === array_key_exists($key, $this->values)) {
            $value = $callback();

            if (is_numeric($value)) {
                $value = round($value, 2);
            }
            $this->values[$key] = $value;
        }
        return $this->values[$key];
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
