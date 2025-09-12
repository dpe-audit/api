<?php

namespace App\Domain\Ecs\Systeme;

use Webmozart\Assert\Assert;

final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $iecs,
        public readonly ?float $rd,
        public readonly ?float $rs,
        public readonly ?float $rg,
        public readonly ?float $rgs,
    ) {}

    public static function create(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null,
    ): self {
        Assert::nullOrGreaterThanEq($rdim, 0);
        Assert::nullOrLessThanEq($rdim, 1);
        Assert::nullOrGreaterThanEq($iecs, 0);
        Assert::nullOrGreaterThanEq($rd, 0);
        Assert::nullOrGreaterThanEq($rs, 0);
        Assert::nullOrGreaterThanEq($rg, 0);
        Assert::nullOrGreaterThanEq($rgs, 0);

        return new self(
            rdim: $rdim,
            iecs: $iecs,
            rd: $rd,
            rs: $rs,
            rg: $rg,
            rgs: $rgs,
        );
    }

    public function with(
        ?float $rdim = null,
        ?float $iecs = null,
        ?float $rd = null,
        ?float $rs = null,
        ?float $rg = null,
        ?float $rgs = null
    ): self {
        return self::create(
            rdim: $rdim ?? $this->rdim,
            iecs: $iecs ?? $this->iecs,
            rd: $rd ?? $this->rd,
            rs: $rs ?? $this->rs,
            rg: $rg ?? $this->rg,
            rgs: $rgs ?? $this->rgs,
        );
    }
}
