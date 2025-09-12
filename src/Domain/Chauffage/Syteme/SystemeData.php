<?php

namespace App\Domain\Chauffage\Systeme;

use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?Configuration $configuration,
        public readonly ?float $rdim,
        public readonly ?float $rd,
        public readonly ?float $re,
        public readonly ?float $rr,
        public readonly ?float $rg,
        public readonly ?float $ich,
        public readonly ?float $i0,
        public readonly ?float $int,
    ) {}

    public static function create(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $rd = null,
        ?float $re = null,
        ?float $rr = null,
        ?float $rg = null,
        ?float $ich = null,
        ?float $i0 = null,
        ?float $int = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($re, 0);
        Assert::nullOrGreaterThanEq($rr, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($ich, 0);
        Assert::nullOrGreaterThanEq($i0, 0);
        Assert::nullOrGreaterThanEq($int, 0);

        return new self(
            configuration: $configuration,
            rdim: $rdim,
            rd: $rd,
            re: $re,
            rr: $rr,
            rg: $rg,
            ich: $ich,
            i0: $i0,
            int: $int,
        );
    }

    public function with(
        ?Configuration $configuration = null,
        ?float $rdim = null,
        ?float $rd = null,
        ?float $re = null,
        ?float $rr = null,
        ?float $rg = null,
        ?float $ich = null,
        ?float $i0 = null,
        ?float $int = null,
    ): self {
        return self::create(
            configuration: $configuration ?? $this->configuration,
            rdim: $rdim ?? $this->rdim,
            rd: $rd ?? $this->rd,
            re: $re ?? $this->re,
            rr: $rr ?? $this->rr,
            rg: $rg ?? $this->rg,
            ich: $ich ?? $this->ich,
            i0: $i0 ?? $this->i0,
            int: $int ?? $this->int,
        );
    }
}
