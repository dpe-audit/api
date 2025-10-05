<?php

namespace App\Domain\Ecs;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use Webmozart\Assert\Assert;

final class EcsData
{
    public function __construct(
        public readonly ?float $nmax,
        public readonly ?float $nadeq,
        public readonly ?float $becs,
        public readonly ?PerteCollection $pertes,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThan($nmax, 0);
        Assert::nullOrGreaterThan($nadeq, 0);
        Assert::nullOrGreaterThan($becs, 0);

        return new self(
            nmax: $nmax,
            nadeq: $nadeq,
            becs: $becs,
            pertes: $pertes,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $nmax = null,
        ?float $nadeq = null,
        ?float $becs = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            nmax: $nmax ?? $this->nmax,
            nadeq: $nadeq ?? $this->nadeq,
            becs: $becs ?? $this->becs,
            pertes: $pertes ?? $this->pertes,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
