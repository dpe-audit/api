<?php

namespace App\Engine;

/**
 * @template T of Input
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
    public function item(): Input
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

    public function __invoke(Context $context): void
    {
        $this->context = $context;
        $this->rewind();

        foreach ($this as $rule) {
            if (false === $context->store()->has($rule->namespace())) {
                $rule->calcule();
            }
        }
    }
}
