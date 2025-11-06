<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;

/**
 * @property array<string, Store> $stores
 */
final class Context
{
    /** @var Store[] */
    private array $stores = [];

    public function __construct(
        private Engine $engine,
        private Input $input,
        private ScenarioUsage $scenario,
    ) {}

    public static function create(Engine $engine, Input $input, ?ScenarioUsage $scenario = null): self
    {
        return new self(
            engine: $engine,
            input: $input,
            scenario: $scenario ?? ScenarioUsage::CONVENTIONNEL,
        );
    }

    public function restore(): void
    {
        $this->scenario = ScenarioUsage::CONVENTIONNEL;
        $this->stores = [];
    }

    public function switch(ScenarioUsage $scenario): void
    {
        $this->scenario = $scenario;
    }

    public function engine(): Engine
    {
        return $this->engine;
    }

    public function input(): Input
    {
        return $this->input;
    }

    public function scenario(): ScenarioUsage
    {
        return $this->scenario;
    }

    public function store(): Store
    {
        $key = $this->scenario->value;
        if (false === array_key_exists($key, $this->stores)) {
            $this->stores[$key] = new Store();
        }
        return $this->stores[$key];
    }

    public function rules(): Rules
    {
        return $this->engine->rules();
    }
}
