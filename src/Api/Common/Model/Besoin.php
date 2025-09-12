<?php

namespace App\Api\Common\Model;

use App\Model\Common\Enum\{ScenarioUsage, Usage};
use App\Model\Common\ValueObject\Besoins as Value;

final class Besoin
{
    public function __construct(
        public readonly Usage $usage,
        public readonly ScenarioUsage $scenario,
        public readonly float $besoin,
    ) {}

    /**
     * @return self[]
     */
    public static function from(Value $value): array
    {
        $values = [];

        foreach ($value->usages() as $usage) {
            foreach ($value->scenarios() as $scenario) {
                $values[] = new self(
                    usage: $usage,
                    scenario: $scenario,
                    besoin: $value->get(scenario: $scenario, usage: $usage),
                );
            }
        }
        return $values;
    }
}
