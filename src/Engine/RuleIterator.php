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

    /**
     * Données d'entrée
     * 
     * @return T
     */
    public function item(): mixed
    {
        return $this->collection()[$this->position()];
    }

    public function supports(): bool
    {
        return true;
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->cache = null;
        $this->skip();
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
        $this->skip();
    }

    public function position(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        if ($this->cache === null) {
            $this->cache = $this->collection();
        }
        return isset($this->cache[$this->position]);
    }

    private function skip(): void
    {
        if ($this->cache === null) {
            $this->cache = $this->collection();
        }
        while (isset($this->cache[$this->position]) && !$this->supports()) {
            ++$this->position;
        }
    }

    public function __invoke(mixed $data, Context $context): void
    {
        $this->setContext($context);
        $this->rewind();
    }
}
