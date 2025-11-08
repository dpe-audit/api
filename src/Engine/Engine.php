<?php

namespace App\Engine;

use App\Domain\Common\Enum\ScenarioUsage;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @property Rules|RuleInterface[] $rules
 */
final class Engine
{
    public function __construct(
        #[AutowireIterator('app.engine.rule')]
        private iterable $rules,
    ) {}

    /**
     * @return Rules|RuleInterface[]
     */
    public function rules(): Rules
    {
        return $this->rules;
    }

    public function __invoke(mixed $data, Input $input, ScenarioUsage $scenario): mixed
    {
        $rules = new Rules($this->rules);
        $context = Context::create(rules: $rules, input: $input, scenario: $scenario);

        foreach ($this->rules as $rule) {
            //$time = new \DateTime;
            $rule->__invoke($data, $context);
            //$duration = (new \DateTime)->diff($time);
            //echo $rule::class . '|' . $duration->f . "\n";
        }
        return $data;
    }
}
