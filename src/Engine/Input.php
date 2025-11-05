<?php

namespace App\Engine;

abstract class Input
{
    public readonly Context $context;

    public function require(string $class): RuleInterface
    {
        if (null === $rule = $this->context->engine()->rules()->find($class)) {
            throw new \DomainException(sprintf("Dépendance %s non trouvée", $class));
        }
        $rule($this->context);
        return $rule;
    }

    public function requireIterator(string $class, Input $item): RuleIterator
    {
        foreach ($this->context->engine()->rules()->search($class) as $iterator) {
            if (!$iterator instanceof RuleIterator) {
                continue;
            }
            foreach ($iterator as $rule) {
                if ($rule->item() === $item) {
                    $rule($this->context);
                    return $rule;
                }
            }
        }
        throw new \DomainException(sprintf("Dépendance %s non trouvée", $class));
    }
}
