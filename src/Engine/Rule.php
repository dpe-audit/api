<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ressource\Ressource;
use App\Engine\Input\RessourceInput;

abstract class Rule implements RuleInterface
{
    protected Engine $context;

    public function context(): Engine
    {
        return $this->context;
    }

    public function ressource(): Ressource
    {
        return $this->context->ressource();
    }

    public function data(): RessourceInput
    {
        return $this->context->data();
    }

    public function scenario(): ScenarioUsage
    {
        return $this->context->scenario();
    }

    public function rules(): Rules
    {
        return $this->context->rules();
    }

    public function get(string $key, callable $cb): mixed
    {
        return $this->context->store()->get($this->namespace(), $key, $cb);
    }

    public static function round(int|float $value): float
    {
        return round($value, 2);
    }

    /**
     * Mutation des données
     */
    public function calcule(): void
    {
        return;
    }

    public function namespace(): string
    {
        return static::class;
    }

    public function __invoke(Engine $context): void
    {
        $this->context = $context;

        if (false === $context->store()->has($this->namespace())) {
            $this->calcule();
        }
    }
}
