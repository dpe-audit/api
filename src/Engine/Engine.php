<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;

/**
 * @property Rules|RuleInterface[] $rules
 */
final class Engine
{
    public function __construct(private Rules $rules) {}

    /**
     * @return Rules|RuleInterface[]
     */
    public function rules(): Rules
    {
        return $this->rules;
    }

    public function __invoke(mixed $data, Input $input,  ScenarioUsage $scenario): mixed
    {
        $context = Context::create(engine: $this, input: $input, scenario: $scenario);

        foreach ($this->rules as $rule) {
            //$time = new \DateTime;
            $rule->__invoke($data, $context);
            //$duration = (new \DateTime)->diff($time);
            //echo $rule::class . '|' . $duration->f . "\n";
        }
        return $data;
    }
}
