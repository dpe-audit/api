<?php

namespace App\Domain\Common\Consommation;

use App\Domain\Common\Enum\{Energie, Usage};
use Webmozart\Assert\Assert;

final class Consommation
{
    public function __construct(
        public readonly Energie $energie,
        public readonly Usage $usage,
        public readonly float $cef,
        public readonly float $cep,
        public readonly float $eges,
    ) {}

    public static function create(
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
            energie: $energie,
            usage: $usage,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }
}
