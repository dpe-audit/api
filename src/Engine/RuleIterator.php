<?php

namespace App\Engine;

/**
 * @template T
 */
abstract class RuleIterator extends Rule implements \Iterator
{
    private int $position = 0;

    /**
     * @return array<T>
     */
    abstract public function collection(): array;

    /**
     * Données d'entrée
     * 
     * @return T
     */
    public function item(): mixed
    {
        return $this->collection()[$this->position()];
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): static
    {
        return $this;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function position(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position(), $this->collection());
    }

    public function __invoke(mixed $data, Context $context): void
    {
        $this->setContext($context);
        $this->rewind();
    }
}
