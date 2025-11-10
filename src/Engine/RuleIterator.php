<?php

namespace App\Engine;

/**
 * @template T
 */
abstract class RuleIterator extends Rule implements \Iterator
{
    private int $position = 0;
    private ?array $cache = null;

    /**
     * @return array<T>
     */
    abstract public function collection(): array;

    public function supports(): bool
    {
        return true;
    }

    /**
     * Données d'entrée
     * 
     * @return T
     */
    final public function item(): mixed
    {
        return $this->cache()[$this->position];
    }

    final public function rewind(): void
    {
        $this->position = 0;
        $this->cache = null;
        $this->skip();
    }

    final public function current(): static
    {
        return $this;
    }

    final public function key(): int
    {
        return $this->position;
    }

    final public function next(): void
    {
        ++$this->position;
        $this->skip();
    }

    final public function position(): int
    {
        return $this->position;
    }

    final public function valid(): bool
    {
        return isset($this->cache()[$this->position]);
    }

    /**
     * @return array<T>
     */
    public function cache(): array
    {
        return $this->cache ??= $this->collection();
    }

    public function skip(): void
    {
        while (isset($this->cache()[$this->position]) && !$this->supports()) {
            ++$this->position;
        }
    }

    public function __invoke(mixed $data, Context $context): void
    {
        $this->setContext($context);
        $this->rewind();
    }
}
