<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;

abstract class Rule implements RuleInterface
{
    private Context $context;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    public function context(): Context
    {
        return $this->context;
    }

    /**
     * Scénario applicable
     */
    public function scenario(): ScenarioUsage
    {
        return $this->context->scenario();
    }

    /**
     * Données d'entrées
     */
    public function input(): Input
    {
        return $this->context->input();
    }

    /**
     * Mémorisation des calculs
     */
    public function get(string $key, callable $cb): mixed
    {
        return $this->context->store()->get($this->namespace(), $key, $cb);
    }

    /**
     * Dépendance
     * 
     * @template U
     * @param class-string<U> $className
     * @return U
     */
    public function require(string $className): RuleInterface
    {
        if (null === $rule = $this->context->rules()->find($className)) {
            throw new \DomainException(sprintf("Règle %s non trouvée", $className));
        }
        $rule->setContext($this->context);
        return $rule;
    }

    /**
     * Dépendance itérable
     * 
     * @template U
     * @param class-string<U> $className
     * @param mixed $item
     * @return U
     */
    public function requireIterator(string $className, mixed $item): RuleIterator
    {
        foreach ($this->context->rules()->search($className) as $iterator) {
            if (!$iterator instanceof RuleIterator) {
                continue;
            }
            foreach ($iterator as $rule) {
                if ($rule->item() === $item) {
                    $rule->setContext($this->context);
                    return $rule;
                }
            }
        }
        throw new \DomainException(sprintf("Règle %s non trouvée", $className));
    }

    public static function round(int|float $value): float
    {
        return round($value, 2);
    }

    public function namespace(): string
    {
        return static::class;
    }

    public function __invoke(mixed $data, Context $context): void
    {
        $this->setContext($context);
    }
}
