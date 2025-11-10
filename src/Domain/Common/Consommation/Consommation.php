<?php

namespace App\Domain\Common\Consommation;

use App\Domain\Common\Enum\{Energie, Scenario, Usage};
use Webmozart\Assert\Assert;

final class Consommation
{
    public function __construct(
        public readonly Scenario $scenario,
        public readonly Energie $energie,
        public readonly Usage $usage,
        public readonly float $cef,
        public readonly float $cep,
        public readonly float $eges,
    ) {}

    public static function create(
        Scenario $scenario,
        Energie $energie,
        Usage $usage,
        float $cef,
        float $cep,
        float $eges,
    ): self {
        Assert::greaterThanEq($cef, 0);
        Assert::greaterThanEq($cep, 0);
        Assert::greaterThanEq($eges, 0);

        return new self(
            scenario: $scenario,
            energie: $energie,
            usage: $usage,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }

    public function __normalize(): array
    {
        return [
            'scenario' => $this->scenario->value,
            'energie' => $this->energie->value,
            'usage' => $this->usage->value,
            'cef' => $this->cef,
            'cep' => $this->cep,
            'eges' => $this->eges,
        ];
    }
}
