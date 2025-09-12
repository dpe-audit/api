<?php

namespace App\Domain\Enveloppe\Lnc\Baie;

use Webmozart\Assert\Assert;

final class BaieData
{
    public function __construct(
        public readonly ?float $aue,
        public readonly ?float $aiu,
        public readonly ?float $sst,
    ) {}

    public static function create(
        ?float $aue = null,
        ?float $aiu = null,
        ?float $sst = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);
        Assert::nullOrGreaterThanEq($sst, 0);

        return new self(
            aue: $aue,
            aiu: $aiu,
            sst: $sst,
        );
    }

    public function with(
        ?float $aue = null,
        ?float $aiu = null,
        ?float $sst = null,
    ): self {
        return self::create(
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
            sst: $sst ?? $this->sst,
        );
    }
}
