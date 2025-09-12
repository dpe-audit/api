<?php

namespace App\Domain\Enveloppe\Lnc\Paroi;

use Webmozart\Assert\Assert;

final class ParoiData
{
    public function __construct(
        public readonly ?float $aue,
        public readonly ?float $aiu,
    ) {}

    public static function create(
        ?float $aue = null,
        ?float $aiu = null,
    ): self {
        Assert::nullOrGreaterThanEq($aue, 0);
        Assert::nullOrGreaterThanEq($aiu, 0);

        return new self(
            aue: $aue,
            aiu: $aiu,
        );
    }

    public function with(
        ?float $aue = null,
        ?float $aiu = null,
    ): self {
        return self::create(
            aue: $aue ?? $this->aue,
            aiu: $aiu ?? $this->aiu,
        );
    }
}
