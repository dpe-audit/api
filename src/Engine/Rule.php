<?php

namespace App\Engine;

abstract class Rule implements RuleInterface
{
    protected Context $context;

    public function context(): Context
    {
        return $this->context;
    }

    public function get(string $key, callable $cb): mixed
    {
        return $this->context->store()->get($this->namespace(), $key, $cb);
    }

    public static function round(int|float $value): float
    {
        return round($value, 2);
    }

    public function namespace(): string
    {
        return static::class;
    }

    /**
     * Mutation des données
     */
    public function calcule(): void
    {
        return;
    }

    public function __invoke(Context $context): void
    {
        $this->context = $context;

        if (false === $context->store()->has($this->namespace())) {
            $this->calcule();
        }
    }
}
