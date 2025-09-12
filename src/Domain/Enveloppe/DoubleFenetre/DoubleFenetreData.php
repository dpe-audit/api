<?php

namespace App\Domain\Enveloppe\DoubleFenetre;

use Webmozart\Assert\Assert;

final class DoubleFenetreData
{
    public function __construct(
        public readonly ?float $ug,
        public readonly ?float $uw,
        public readonly ?float $sw,
    ) {}

    public static function create(
        ?float $ug = null,
        ?float $uw = null,
        ?float $sw = null,
    ): self {
        Assert::nullOrGreaterThanEq($ug, 0);
        Assert::nullOrGreaterThanEq($uw, 0);
        Assert::nullOrGreaterThanEq($sw, 0);

        return new self(
            ug: $ug,
            uw: $uw,
            sw: $sw,
        );
    }

    public function with(
        ?float $ug = null,
        ?float $uw = null,
        ?float $sw = null,
    ): self {
        return self::create(
            ug: $ug ?? $this->ug,
            uw: $uw ?? $this->uw,
            sw: $sw ?? $this->sw,
        );
    }
}
