<?php

namespace App\Database\Local;

final class XMLTableCollection implements \Countable, \IteratorAggregate
{
    public function __construct(private array $values) {}

    public function from(array $values): static
    {
        return new static($values);
    }

    public function count(): int
    {
        return \count($this->values);
    }

    public function first(): ?XMLTableElement
    {
        return $this->count() ? current($this->values) : null;
    }

    public function last(): ?XMLTableElement
    {
        return $this->count() ? end($this->values) : null;
    }

    /**
     * @return array<array{x: float, y: float, q?: float}>
     */
    public function points(string $x, string $y, ?string $q = null): array
    {
        $points = [];
        /** @var XMLTableElement $element */
        foreach ($this->values as $element) {
            $row = ['x' => $element->floatval($x), 'y' => $element->floatval($y)];
            if ($q) {
                $row['q'] = $element->floatval($q);
            }
            $points[] = $row;
        }
        return $points;
    }

    public function slice(int $offset, ?int $length): static
    {
        return new static(\array_slice($this->values, $offset, $length));
    }

    public function find(string $name, mixed $value): ?XMLTableElement
    {
        $value = (string) $value;
        return array_find($this->values, fn(XMLTableElement $element): bool => $element->strval($name) === $value);
    }

    public function filter(\Closure $p): static
    {
        return $this->from(array_filter($this->values, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function map(\Closure $p): static
    {
        return $this->from(array_map($p, $this->values));
    }

    public function reduce(\Closure $func, mixed $initial = 0): mixed
    {
        return array_reduce($this->values, $func, $initial);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    public function values(): array
    {
        return $this->values;
    }
}
