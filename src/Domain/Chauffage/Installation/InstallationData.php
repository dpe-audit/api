<?php

namespace App\Domain\Chauffage\Installation;

use App\Domain\Common\Consommation\ConsommationCollection;
use App\Domain\Common\Perte\PerteCollection;
use Webmozart\Assert\Assert;

final class InstallationData
{
    public function __construct(
        public readonly ?float $fch,
        public readonly ?float $rdim,
        public readonly ?float $i0,
        public readonly ?float $int,
        public readonly ?float $ich,
        public readonly ?float $re,
        public readonly ?float $rd,
        public readonly ?float $rg,
        public readonly ?float $rr,
        public readonly ?PerteCollection $pertes,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?float $fch = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        Assert::nullOrGreaterThanEq($fch, 0);
        Assert::nullOrLessThanEq($fch, 1);
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($i0, 0);
        Assert::nullOrGreaterThanEq($int, 0);
        Assert::nullOrGreaterThanEq($ich, 0);
        Assert::nullOrGreaterThanEq($re, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($rr, 0);

        return new self(
            fch: $fch,
            rdim: $rdim,
            i0: $i0,
            int: $int,
            ich: $ich,
            re: $re,
            rd: $rd,
            rg: $rg,
            rr: $rr,
            pertes: $pertes,
            consommations: $consommations,
        );
    }

    public function with(
        ?float $fch = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?PerteCollection $pertes = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            fch: $fch ?? $this->fch,
            rdim: $rdim ?? $this->rdim,
            i0: $i0 ?? $this->i0,
            int: $int ?? $this->int,
            ich: $ich ?? $this->ich,
            re: $re ?? $this->re,
            rd: $rd ?? $this->rd,
            rg: $rg ?? $this->rg,
            rr: $rr ?? $this->rr,
            pertes: $pertes ?? $this->pertes,
            consommations: $consommations ?? $this->consommations,
        );
    }
}
