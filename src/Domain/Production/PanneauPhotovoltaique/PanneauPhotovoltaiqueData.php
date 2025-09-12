<?php

namespace App\Domain\Production\PanneauPhotovoltaique;

use Webmozart\Assert\Assert;

final class PanneauPhotovoltaiqueData
{
    public function __construct(
        public readonly ?float $kpv,
        public readonly ?float $ppv,
    ) {}

    public static function create(
        ?float $kpv = null,
        ?float $ppv = null,
    ): self {
        Assert::nullOrGreaterThanEq($kpv, 0);
        Assert::nullOrGreaterThanEq($ppv, 0);

        return new self(
            kpv: $kpv,
            ppv: $ppv,
        );
    }

    public function with(
        ?float $kpv = null,
        ?float $ppv = null,
    ): self {
        return self::create(
            kpv: $kpv ?? $this->kpv,
            ppv: $ppv ?? $this->ppv,
        );
    }
}
