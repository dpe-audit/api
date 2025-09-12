<?php

namespace App\Domain\Ecs;

use Webmozart\Assert\Assert;

final class EcsData
{
    public function __construct(
        public readonly ?float $nmax,
        public readonly ?float $nadeq,
        public readonly ?float $becs,
        public readonly ?float $cef,
        public readonly ?float $cep,
        public readonly ?float $eges,
    ) {}

    public static function create(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        Assert::nullOrGreaterThan($nmax, 0);
        Assert::nullOrGreaterThan($nadeq, 0);
        Assert::nullOrGreaterThan($becs, 0);
        Assert::nullOrGreaterThan($cef, 0);
        Assert::nullOrGreaterThan($cep, 0);
        Assert::nullOrGreaterThan($eges, 0);

        return new self(
            nmax: $nmax,
            nadeq: $nadeq,
            becs: $becs,
            cef: $cef,
            cep: $cep,
            eges: $eges,
        );
    }

    public function with(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?float $cef = null,
        ?float $cep = null,
        ?float $eges = null,
    ): self {
        return self::create(
            nmax: $nmax ?? $this->nmax,
            nadeq: $nadeq ?? $this->nadeq,
            becs: $becs ?? $this->becs,
            cef: $cef ?? $this->cef,
            cep: $cep ?? $this->cep,
            eges: $eges ?? $this->eges,
        );
    }
}
