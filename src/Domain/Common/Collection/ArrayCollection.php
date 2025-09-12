<?php

namespace App\Domain\Common\Collection;

/**
 * @template T of object
 */
class ArrayCollection implements Collection
{
    /**
     * @param array<T> $elements
     */
    public function __construct(protected array $elements = []) {}

    /**
     * @param array<T> $elements
     */
    protected function createFrom(array $elements): static
    {
        return new static($elements);
    }

    public static function fromCollections(self ...$collections): static
    {
        $collection = [];
        foreach ($collections as $item) {
            $collection = \array_merge($collection, $item->values());
        }
        return new static($collection);
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @return array<T>
     */
    public function to_array(): array
    {
        return $this->elements;
    }

    /**
     * @return ?T
     */
    public function first(): ?object
    {
        return $this->count() ? reset($this->elements) : null;
    }

    /**
     * @return ?T
     */
    public function last(): ?object
    {
        return $this->count() ? end($this->elements) : null;
    }

    public function key(): string|int|null
    {
        return key($this->elements);
    }

    /**
     * @return false|T
     */
    public function next(): mixed
    {
        return next($this->elements);
    }

    /**
     * @return ?T
     */
    public function current(): mixed
    {
        return current($this->elements);
    }

    public function has(\Closure $p): bool
    {
        return array_find($this->elements, $p) !== null;
    }

    public function containsKey(string|int $key)
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    /**
     * @param T $element
     */
    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function exists(\Closure $p)
    {
        return array_find($this->elements, $p) !== null;
    }

    public function usort(\Closure $p): static
    {
        $elements = [...$this->elements];

        usort($elements, $p);

        return new static($elements);
    }

    public function slice(int $offset, ?int $length): static
    {
        return new static(\array_slice($this->elements, $offset, $length));
    }

    public function filter(\Closure $p): static
    {
        return $this->createFrom(array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function map(\Closure $func): static
    {
        return $this->createFrom(array_map($func, $this->elements));
    }

    public function unique(): static
    {
        return $this->createFrom(array_unique($this->elements, SORT_REGULAR));
    }

    public function walk(\Closure $func): static
    {
        array_walk($this->elements, $func);
        return $this;
    }

    public function reduce(\Closure $func, mixed $initial = 0): mixed
    {
        return array_reduce($this->elements, $func, $initial);
    }

    public function merge(?self ...$collections): static
    {
        $collection = $this->elements;
        foreach ($collections as $item) {
            $collection = \array_merge($collection, $item ? $item->values() : []);
        }
        return new static($collection);
    }

    /**
     * @return array<T>
     */
    public function values(): array
    {
        return $this->elements;
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    public function indexOf($element): string|int|false
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * @return ?T
     */
    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * @param T $value
     */
    public function set(string|int $key, mixed $value): void
    {
        $this->elements[$key] = $value;
    }

    /**
     * @param T $element
     */
    public function add(mixed $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * @param T $element
     */
    public function remove(mixed $element): bool
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }
}
