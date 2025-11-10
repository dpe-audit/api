<?php

namespace App\Engine;

/**
 * @property array<RuleInterface> $rules
 */
final class Rules implements \IteratorAggregate
{
    private iterable $rules;

    /**
     * @param array<RuleInterface> $rules
     */
    public function __construct(iterable $rules)
    {
        $this->rules = [];
        foreach ($rules as $rule) {
            $this->rules[] = clone $rule;
        }
    }

    public function find(string $class): ?RuleInterface
    {
        foreach ($this->rules as $rule) {
            if ($rule::class === $class) {
                return $rule;
            }
        }
        return null;
    }

    /**
     * @return array<RuleInterface>
     */
    public function search(string $class): array
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            if (is_a($rule, $class)) {
                $rules[] = $rule;
            }
        }
        return $rules;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->rules);
    }
}
