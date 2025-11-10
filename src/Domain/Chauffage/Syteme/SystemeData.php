<?php

namespace App\Domain\Chauffage\Systeme;

use App\Domain\Common\Consommation\ConsommationCollection;
use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?Configuration $configuration,
        public readonly ?float $rdim,
        public readonly ?float $i0,
        public readonly ?float $int,
        public readonly ?float $ich,
        public readonly ?float $re,
        public readonly ?float $rd,
        public readonly ?float $rg,
        public readonly ?float $rr,
        public readonly ?ConsommationCollection $consommations,
    ) {}

    public static function create(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?ConsommationCollection $consommations = null,
    ): self {
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
            configuration: $configuration,
            rdim: $rdim,
            i0: $i0,
            int: $int,
            ich: $ich,
            re: $re,
            rd: $rd,
            rg: $rg,
            rr: $rr,
            consommations: $consommations,
        );
    }

    public function with(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $i0 = null,
        ?float $int = null,
        ?float $ich = null,
        ?float $re = null,
        ?float $rd = null,
        ?float $rg = null,
        ?float $rr = null,
        ?ConsommationCollection $consommations = null,
    ): self {
        return self::create(
            configuration: $configuration ?? $this->configuration,
            rdim: $rdim ?? $this->rdim,
            i0: $i0 ?? $this->i0,
            int: $int ?? $this->int,
            ich: $ich ?? $this->ich,
            re: $re ?? $this->re,
            rd: $rd ?? $this->rd,
            rg: $rg ?? $this->rg,
            rr: $rr ?? $this->rr,
            consommations: $consommations ?? $this->consommations
        );
    }
}
