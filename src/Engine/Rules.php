<?php

namespace App\Engine;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property array<RuleInterface> $rules
 */
final class Rules implements \IteratorAggregate
{
    public function __construct(
        #[AutowireIterator('app.engine.performance.rule')]
        private iterable $rules,
    ) {}

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

    public function require(string $class): RuleInterface
    {
        if (null === $rule = $this->find($class)) {
            throw new \DomainException(sprintf("Règle %s non trouvée", $class));
        }
        return $rule;
    }

    public function requireIterator(string $class, mixed $data): RuleIterator
    {
        foreach ($this->search($class) as $iterator) {
            if (!$iterator instanceof RuleIterator) {
                continue;
            }
            foreach ($iterator as $rule) {
                if ($rule->item() === $data) {
                    return $rule;
                }
            }
        }
        throw new \DomainException(sprintf("Règle %s non trouvée", $class));
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->rules);
    }
}
