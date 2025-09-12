<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ressource\Ressource;
use App\Engine\Input\RessourceInput;

/**
 * @property Rules|RuleInterface[] $rules
 */
final class Engine
{
    private ScenarioUsage $scenario;
    private Ressource $ressource;
    private RessourceInput $data;
    private Store $store;

    public function __construct(private Rules $rules)
    {
        $this->store = new Store();
    }

    /**
     * @return Rules|RuleInterface[]
     */
    public function rules(): Rules
    {
        return $this->rules;
    }

    public function scenario(): ScenarioUsage
    {
        return $this->scenario;
    }

    public function ressource(): Ressource
    {
        return $this->ressource;
    }

    public function data(): RessourceInput
    {
        return $this->data;
    }

    public function store(): Store
    {
        return $this->store;
    }

    public function __invoke(Ressource $ressource, ScenarioUsage $scenario): Ressource
    {
        $this->ressource = $ressource;
        $this->scenario = $scenario;
        $this->data = new RessourceInput($this);
        $this->store->clear();

        foreach ($this->rules as $rule) {
            //$time = new \DateTime;
            $rule->__invoke(context: $this);
            //$duration = (new \DateTime)->diff($time);
            //echo $rule::class . '|' . $duration->f . "\n";
        }
        return $ressource;
    }
}
